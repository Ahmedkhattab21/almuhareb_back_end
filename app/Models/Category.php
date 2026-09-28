<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const SUPPORTED_LOCALES = ['ar-EG', 'en-US', 'fr-FR', 'hi', 'ur', 'bn', 'si', 'fil', 'ne', 'id'];
    public const RTL_LOCALES = ['ar-EG', 'ur'];

    protected $fillable = [
        'admin_id',
        'name',
        'status',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function lawyers()
    {
        return $this->belongsToMany(
            Lawyer::class,
            'lawyers_categories',
            'category_id',
            'lawyer_id'
        )->withPivot('company_id')->withTimestamps()->distinct();
    }

    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'lawyers_categories',
            'category_id',
            'company_id'
        )->withPivot('lawyer_id')->withTimestamps()->distinct();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public function getTranslatedName(?string $locale = null): string
    {
        $locale = static::normalizeLocale($locale ?: app()->getLocale());
        $translations = $this->loadedTranslationsCollection();

        foreach (array_filter([$locale, config('app.locale'), 'ar-EG', 'en-US']) as $candidate) {
            $candidate = static::normalizeLocale($candidate);
            $translation = $translations->firstWhere('locale', $candidate);
            $name = is_array($translation)
                ? ($translation['name'] ?? null)
                : ($translation?->name);

            if (filled($name)) {
                return $name;
            }
        }

        $fallback = $translations->first(function ($translation) {
            return filled(is_array($translation) ? ($translation['name'] ?? null) : ($translation?->name));
        });

        return (is_array($fallback) ? ($fallback['name'] ?? null) : ($fallback?->name))
            ?? $this->name
            ?? '';
    }

    public function translationsMap(): array
    {
        $translations = $this->loadedTranslationsCollection()
            ->mapWithKeys(function ($translation) {
                $locale = is_array($translation) ? ($translation['locale'] ?? null) : ($translation?->locale);
                $name = is_array($translation) ? ($translation['name'] ?? null) : ($translation?->name);

                return $locale ? [$locale => $name] : [];
            })
            ->all();

        foreach (static::SUPPORTED_LOCALES as $locale) {
            $translations[$locale] ??= '';
        }

        return collect($translations)
            ->only(static::SUPPORTED_LOCALES)
            ->all();
    }

    public function completedTranslationsCount(): int
    {
        $translations = $this->loadedTranslationsCollection();

        return $translations
            ->whereIn('locale', static::SUPPORTED_LOCALES)
            ->filter(fn ($translation) => filled(is_array($translation) ? ($translation['name'] ?? null) : ($translation?->name)))
            ->count();
    }

    private function loadedTranslationsCollection()
    {
        if (! $this->relationLoaded('translations')) {
            return $this->translations()->get();
        }

        return collect($this->getRelation('translations'));
    }

    public static function normalizeLocale(?string $locale): string
    {
        $locale = trim((string) $locale);
        $locale = str_replace('_', '-', $locale);
        $locale = explode(',', $locale)[0] ?? $locale;
        $locale = explode(';', $locale)[0] ?? $locale;

        $supported = collect(static::SUPPORTED_LOCALES)
            ->mapWithKeys(fn (string $supportedLocale) => [strtolower($supportedLocale) => $supportedLocale])
            ->all();

        $lowerLocale = strtolower($locale);

        if (isset($supported[$lowerLocale])) {
            return $supported[$lowerLocale];
        }

        if ($lowerLocale === 'tl' || str_starts_with($lowerLocale, 'tl-')) {
            return 'fil';
        }

        $baseLocale = explode('-', $lowerLocale)[0] ?? '';

        foreach (static::SUPPORTED_LOCALES as $supportedLocale) {
            if (strtolower(explode('-', $supportedLocale)[0]) === $baseLocale) {
                return $supportedLocale;
            }
        }

        return 'ar-EG';
    }

    public static function supportedLanguageOptions(): array
    {
        return [
            'ar-EG' => ['native' => 'العربية', 'direction' => 'rtl', 'sort_order' => 1],
            'en-US' => ['native' => 'English', 'direction' => 'ltr', 'sort_order' => 2],
            'fr-FR' => ['native' => 'Français', 'direction' => 'ltr', 'sort_order' => 3],
            'hi' => ['native' => 'हिन्दी', 'direction' => 'ltr', 'sort_order' => 4],
            'ur' => ['native' => 'اردو', 'direction' => 'rtl', 'sort_order' => 5],
            'bn' => ['native' => 'বাংলা', 'direction' => 'ltr', 'sort_order' => 6],
            'si' => ['native' => 'සිංහල', 'direction' => 'ltr', 'sort_order' => 7],
            'fil' => ['native' => 'Filipino', 'direction' => 'ltr', 'sort_order' => 8],
            'ne' => ['native' => 'नेपाली', 'direction' => 'ltr', 'sort_order' => 9],
            'id' => ['native' => 'Bahasa Indonesia', 'direction' => 'ltr', 'sort_order' => 10],
        ];
    }
}
