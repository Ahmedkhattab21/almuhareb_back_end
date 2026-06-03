<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeminiTextToSpeechService
{
    private const SAMPLE_RATE = 24000;
    private const CHANNELS = 1;
    private const BITS_PER_SAMPLE = 16;

    public function synthesizeToPublicStorage(string $text, ?string $language = null, ?string $directory = null): ?array
    {
        $text = trim(strip_tags($text));

        if ($text === '') {
            Log::warning('Gemini TTS skipped: empty text.');
            return null;
        }

        $apiKey = config('services.gemini.api_key')
            ?: config('services.gemini.key')
            ?: env('GEMINI_API_KEY');

        if (! $apiKey) {
            Log::error('Gemini TTS API key is missing.');
            return null;
        }

        $models = array_values(array_unique(array_filter([
            config('services.gemini.tts_model'),
            env('GEMINI_TTS_MODEL'),
            'gemini-2.5-flash-preview-tts',
            'gemini-2.5-pro-preview-tts',
        ])));

        $prompt = $this->buildPrompt($text, $language);

        $timeout = max((int) config('services.gemini.timeout', 120), 60);

        foreach ($models as $model) {
            try {
                $response = Http::timeout($timeout)
                    ->retry(1, 500)
                    ->withHeaders([
                        'x-goog-api-key' => $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => $prompt,
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'responseModalities' => ['AUDIO'],
                            'speechConfig' => [
                                'voiceConfig' => [
                                    'prebuiltVoiceConfig' => [
                                        'voiceName' => config('services.gemini.tts_voice', 'Kore'),
                                    ],
                                ],
                            ],
                        ],
                    ]);

                if ($response->failed()) {
                    Log::warning('Gemini TTS request failed.', [
                        'model' => $model,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    continue;
                }

                $json = $response->json();

                $base64Audio = data_get($json, 'candidates.0.content.parts.0.inlineData.data')
                    ?: data_get($json, 'candidates.0.content.parts.0.inline_data.data');

                if (! $base64Audio) {
                    Log::warning('Gemini TTS returned no audio data.', [
                        'model' => $model,
                        'response' => $json,
                    ]);

                    continue;
                }

                $pcm = base64_decode($base64Audio, true);

                if ($pcm === false || $pcm === '') {
                    Log::warning('Gemini TTS audio decode failed.', [
                        'model' => $model,
                    ]);

                    continue;
                }

                $wav = $this->pcmToWav($pcm);

                $directory = trim($directory ?: 'tickets/ai-audio', '/');

                $path = $directory
                    . '/ai-reply-'
                    . now()->format('YmdHis')
                    . '-'
                    . Str::random(8)
                    . '.wav';

                Storage::disk('public')->put($path, $wav);

                return [
                    'path' => $path,
                    'name' => basename($path),
                    'mime_type' => 'audio/wav',
                    'file_type' => 'audio',
                    'size' => strlen($wav),
                    'url' => asset('storage/' . $path),
                ];
            } catch (\Throwable $exception) {
                Log::warning('Gemini TTS exception.', [
                    'model' => $model,
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);
            }
        }

        return null;
    }

private function buildPrompt(string $text, ?string $language): string
{
    $languageName = $this->languageName($language) ?: 'the same language';

    $text = trim(strip_tags($text));

    return <<<PROMPT
Read this text aloud in {$languageName}. Only generate spoken audio. Do not answer, do not explain, do not summarize, and do not translate.

{$text}
PROMPT;
}
    private function languageName(?string $language): ?string
    {
        $language = is_string($language) ? strtolower(trim($language)) : null;

        if (! $language) {
            return null;
        }

        $language = str_replace('_', '-', $language);

        $shortCode = explode('-', $language)[0];

        return [
            'ar' => 'Arabic',
            'en' => 'English',
            'ur' => 'Urdu',
            'hi' => 'Hindi',
            'bn' => 'Bengali',
            'fil' => 'Filipino',
            'tl' => 'Filipino',
            'id' => 'Indonesian',
            'ne' => 'Nepali',
            'si' => 'Sinhala',
            'ta' => 'Tamil',
            'ml' => 'Malayalam',
            'te' => 'Telugu',
            'am' => 'Amharic',
            'sw' => 'Swahili',
            'fr' => 'French',
            'es' => 'Spanish',
            'tr' => 'Turkish',
        ][$shortCode] ?? $language;
    }

    private function pcmToWav(string $pcm): string
    {
        $dataSize = strlen($pcm);

        $byteRate = (int) (
            self::SAMPLE_RATE
            * self::CHANNELS
            * (self::BITS_PER_SAMPLE / 8)
        );

        $blockAlign = (int) (
            self::CHANNELS
            * (self::BITS_PER_SAMPLE / 8)
        );

        return 'RIFF'
            . pack('V', 36 + $dataSize)
            . 'WAVE'
            . 'fmt '
            . pack('V', 16)
            . pack('v', 1)
            . pack('v', self::CHANNELS)
            . pack('V', self::SAMPLE_RATE)
            . pack('V', $byteRate)
            . pack('v', $blockAlign)
            . pack('v', self::BITS_PER_SAMPLE)
            . 'data'
            . pack('V', $dataSize)
            . $pcm;
    }
}
