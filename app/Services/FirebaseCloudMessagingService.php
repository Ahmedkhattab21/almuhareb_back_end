<?php

namespace App\Services;

use App\Models\Notifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FirebaseCloudMessagingService
{
    private const OAUTH_SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public function send(string $token, Notifications $notification): bool
    {
        $token = trim($token);

        if ($token === '') {
            return false;
        }

        $projectId = $this->projectId();
        $accessToken = $this->accessToken();

        if (! $projectId || ! $accessToken) {
            return false;
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $notification->title,
                        'body' => $notification->body ?? '',
                    ],
                    'data' => $this->dataPayload($notification),
                    'android' => [
                        'priority' => 'HIGH',
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            return true;
        }

        Log::warning('Failed to send FCM notification.', [
            'notification_id' => $notification->id,
            'status' => $response->status(),
            'body' => $response->json() ?: $response->body(),
        ]);

        return false;
    }

    private function accessToken(): ?string
    {
        $credentials = $this->credentials();

        if (! $credentials) {
            return null;
        }

        $cacheKey = 'firebase_fcm_access_token_' . md5($credentials['client_email'] ?? 'default');

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($credentials) {
            $jwt = $this->jwt($credentials);

            if (! $jwt) {
                return null;
            }

            $response = Http::asForm()->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $response->successful()) {
                Log::warning('Failed to create Firebase access token.', [
                    'status' => $response->status(),
                    'body' => $response->json() ?: $response->body(),
                ]);

                return null;
            }

            return $response->json('access_token');
        });
    }

    private function jwt(array $credentials): ?string
    {
        $clientEmail = $credentials['client_email'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;
        $tokenUri = $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token';

        if (! $clientEmail || ! $privateKey) {
            return null;
        }

        $now = time();

        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ], JSON_UNESCAPED_SLASHES));

        $claim = $this->base64UrlEncode(json_encode([
            'iss' => $clientEmail,
            'scope' => self::OAUTH_SCOPE,
            'aud' => $tokenUri,
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_UNESCAPED_SLASHES));

        $unsigned = "{$header}.{$claim}";

        $signed = openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (! $signed) {
            Log::warning('Failed to sign Firebase JWT.');

            return null;
        }

        return $unsigned . '.' . $this->base64UrlEncode($signature);
    }

    private function credentials(): ?array
    {
        $path = config('services.firebase.credentials');

        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        $absolutePath = $this->absoluteCredentialsPath($path);

        if (! is_file($absolutePath)) {
            Log::warning('Firebase credentials file was not found.', [
                'path' => $path,
            ]);

            return null;
        }

        $credentials = json_decode(file_get_contents($absolutePath) ?: '', true);

        if (! is_array($credentials)) {
            Log::warning('Firebase credentials file is not valid JSON.', [
                'path' => $path,
            ]);

            return null;
        }

        return $credentials;
    }

    private function absoluteCredentialsPath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        return storage_path('app/' . ltrim($path, '/\\'));
    }

    private function projectId(): ?string
    {
        $projectId = config('services.firebase.project_id');

        return is_string($projectId) && trim($projectId) !== '' ? trim($projectId) : null;
    }

    private function dataPayload(Notifications $notification): array
    {
        $data = collect($notification->data ?? [])
            ->map(fn ($value) => is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE))
            ->all();

        return array_filter(array_merge($data, [
            'notification_id' => (string) $notification->id,
            'type' => (string) $notification->type,
            'entity_type' => $notification->entity_type ? (string) $notification->entity_type : null,
            'entity_id' => $notification->entity_id ? (string) $notification->entity_id : null,
            'url' => $notification->url ? (string) $notification->url : null,
        ]), fn ($value) => $value !== null);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
