<?php

namespace App\Services;

use App\Models\Notifications;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class WorkerLocalizationService
{
    public const SUPPORTED = ['ar', 'en', 'fr', 'hi', 'ur', 'bn', 'si', 'fil', 'ne', 'id'];

    public function localeFor(?Worker $worker = null, ?Request $request = null): string
    {
        $code = $worker?->preferredLanguageCode()
            ?? $request?->header('lang')
            ?? $request?->header('X-Language')
            ?? $request?->header('Accept-Language')
            ?? 'ar';

        return $this->normalize($code);
    }

    public function setLocale(?Worker $worker = null, ?Request $request = null): string
    {
        $locale = $this->localeFor($worker, $request);
        App::setLocale($locale);

        return $locale;
    }

    public function api(string $key, array $replace = [], ?Worker $worker = null, ?Request $request = null): string
    {
        return __("worker_api.{$key}", $replace, $this->localeFor($worker, $request));
    }

    public function localizedNotificationForWorker(Worker $worker, string $type, string $title, ?string $body, ?array $data = null): array
    {
        $locale = $this->localeFor($worker);
        $replace = array_merge($this->stringableData($data ?? []), [
            'title' => $title,
            'body' => (string) $body,
        ]);

        $translatedTitle = __("worker_notifications.types.{$type}.title", $replace, $locale);
        $translatedBody = __("worker_notifications.types.{$type}.body", $replace, $locale);

        return [
            'title' => $translatedTitle !== "worker_notifications.types.{$type}.title" ? $translatedTitle : $title,
            'body' => $translatedBody !== "worker_notifications.types.{$type}.body" ? $translatedBody : $body,
        ];
    }

    public function notificationPayload(Worker $worker, Notifications $notification): array
    {
        $localized = $this->localizedNotificationForWorker(
            $worker,
            (string) $notification->type,
            (string) $notification->title,
            $notification->body,
            $notification->data ?? []
        );

        return [
            'title' => $localized['title'],
            'body' => $localized['body'],
        ];
    }

    public function normalize(?string $code): string
    {
        $code = Str::lower(trim((string) $code));
        $code = str_replace('_', '-', $code);
        $code = explode(',', $code)[0] ?? $code;
        $code = explode(';', $code)[0] ?? $code;

        if (Str::startsWith($code, 'fil') || Str::startsWith($code, 'tl')) {
            return 'fil';
        }

        $short = explode('-', $code)[0] ?: 'ar';

        return in_array($short, self::SUPPORTED, true) ? $short : 'ar';
    }

    private function stringableData(array $data): array
    {
        return collect($data)
            ->map(fn ($value) => is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE))
            ->all();
    }
}
