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
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            return null;
        }

        $models = array_values(array_unique(array_filter([
            config('services.gemini.tts_model', 'gemini-3.1-flash-tts-preview'),
            'gemini-3.1-flash-tts-preview',
            'gemini-2.5-flash-preview-tts',
        ])));

        $prompt = $this->buildPrompt($text, $language);
        $timeout = (int) config('services.gemini.timeout', 20);

        foreach ($models as $model) {
            try {
                $response = Http::timeout($timeout)
                    ->retry(1, 300, null, false)
                    ->withHeaders([
                        'x-goog-api-key' => $apiKey,
                    ])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
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

                $base64Audio = (string) (
                    data_get($response->json(), 'candidates.0.content.parts.0.inlineData.data')
                    ?: data_get($response->json(), 'candidates.0.content.parts.0.inline_data.data')
                );

                if ($base64Audio === '') {
                    Log::warning('Gemini TTS returned no audio data.', ['model' => $model]);

                    continue;
                }

                $pcm = base64_decode($base64Audio, true);

                if ($pcm === false || $pcm === '') {
                    Log::warning('Gemini TTS audio decode failed.', ['model' => $model]);

                    continue;
                }

                $wav = $this->pcmToWav($pcm);
                $directory = trim($directory ?: 'tickets/ai-audio', '/');
                $path = $directory.'/ai-reply-'.now()->format('YmdHis').'-'.Str::random(8).'.wav';

                Storage::disk('public')->put($path, $wav);

                return [
                    'path' => $path,
                    'name' => basename($path),
                    'mime_type' => 'audio/wav',
                    'file_type' => 'audio',
                    'size' => strlen($wav),
                ];
            } catch (\Throwable $exception) {
                Log::warning('Gemini TTS exception.', [
                    'model' => $model,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return null;
    }

    private function buildPrompt(string $text, ?string $language): string
    {
        $language = $this->languageName($language);

        if (! $language) {
            return "Read the following text aloud exactly as written. Do not add, remove, translate, summarize, or explain anything:\n\n{$text}";
        }

        return "Read the following text aloud in {$language} exactly as written. Do not add, remove, translate, summarize, or explain anything:\n\n{$text}";
    }

    private function languageName(?string $language): ?string
    {
        $language = is_string($language) ? strtolower(trim($language)) : null;

        if (! $language) {
            return null;
        }

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
            'tr' => 'Turkish',
        ][$language] ?? $language;
    }

    private function pcmToWav(string $pcm): string
    {
        $byteRate = self::SAMPLE_RATE * self::CHANNELS * (self::BITS_PER_SAMPLE / 8);
        $blockAlign = self::CHANNELS * (self::BITS_PER_SAMPLE / 8);
        $dataSize = strlen($pcm);

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
