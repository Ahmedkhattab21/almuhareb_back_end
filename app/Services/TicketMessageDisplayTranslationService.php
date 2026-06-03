<?php

namespace App\Services;

use App\Models\TicketMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TicketMessageDisplayTranslationService
{
    public function attachEnglishForLawyerReplies(Collection $messages): void
    {
        $messages->each(function (TicketMessage $message): void {
            if ($message->sender_type !== 'lawyer') {
                return;
            }

            $message->setAttribute('display_english_message', $this->englishText($message));
        });
    }

    private function englishText(TicketMessage $message): ?string
    {
        $original = trim((string) $message->message_original);
        $translated = trim((string) $message->message_translated);
        $originalLanguage = Str::lower((string) $message->original_language);
        $translatedLanguage = Str::lower((string) $message->translated_language);

        if ($original !== '' && Str::startsWith($originalLanguage, 'en')) {
            return $original;
        }

        if ($translated !== '' && Str::startsWith($translatedLanguage, 'en')) {
            return $translated;
        }

        if ($original === '') {
            return $translated ?: null;
        }

        $cacheKey = 'ticket-message-english:' . $message->id . ':' . md5($original);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($original, $translated, $originalLanguage): ?string {
            return $this->translateToEnglish($original, $originalLanguage ?: 'ar') ?: ($translated ?: null);
        });
    }

    private function translateToEnglish(string $text, string $sourceLanguage): ?string
    {
        $apiKey = config('services.gemini.api_key');

        if (! $apiKey) {
            return null;
        }

        try {
            $models = array_values(array_unique(array_filter([
                config('services.gemini.model', 'gemini-2.5-flash'),
                'gemini-2.5-flash',
                'gemini-flash-latest',
            ])));
            $timeout = (int) config('services.gemini.timeout', 20);

            foreach ($models as $model) {
                $response = Http::timeout($timeout)
                    ->retry(2, 300, null, false)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => "Translate this lawyer reply from {$sourceLanguage} to English. Preserve names, numbers, dates, legal terms, and formatting. Return only the English translation without explanations.\n\nReply:\n{$text}",
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.1,
                        ],
                    ]);

                if ($response->failed()) {
                    continue;
                }

                $translated = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text', ''));

                if ($translated !== '') {
                    return $translated;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to prepare English lawyer reply display.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return null;
    }
}
