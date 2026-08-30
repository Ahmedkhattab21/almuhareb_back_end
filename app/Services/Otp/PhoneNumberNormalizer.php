<?php

namespace App\Services\Otp;

class PhoneNumberNormalizer
{
    public function normalizeSaudi(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $this->toAsciiDigits($phone)) ?? '';

        if (str_starts_with($digits, '00966')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '9665') && strlen($digits) === 12) {
            return $digits;
        }

        if (str_starts_with($digits, '05') && strlen($digits) === 10) {
            return '966'.substr($digits, 1);
        }

        if (str_starts_with($digits, '5') && strlen($digits) === 9) {
            return '966'.$digits;
        }

        return null;
    }

    public function lookupCandidates(string $phone): array
    {
        $normalized = $this->normalizeSaudi($phone);

        if (! $normalized) {
            return [trim($phone)];
        }

        $localWithZero = '0'.substr($normalized, 3);
        $localWithoutZero = substr($normalized, 3);

        return array_values(array_unique([
            trim($phone),
            $normalized,
            '+'.$normalized,
            '00'.$normalized,
            $localWithZero,
            $localWithoutZero,
        ]));
    }

    public function mask(string $normalizedPhone): string
    {
        if (strlen($normalizedPhone) < 6) {
            return '***';
        }

        return substr($normalizedPhone, 0, 4)
            .str_repeat('*', max(strlen($normalizedPhone) - 6, 3))
            .substr($normalizedPhone, -2);
    }

    private function toAsciiDigits(string $value): string
    {
        return strtr($value, [
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
        ]);
    }
}
