<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\LocalizedSlug;
use Illuminate\Support\Collection;

/**
 * The public API of the Localization Engine. Every module reads and writes
 * locales, translations, slugs, and localized URLs exclusively through this
 * contract (or the Localization facade).
 */
interface LocalizationServiceInterface
{
    // --- Locales ---

    /**
     * @return Collection<int, Locale>
     */
    public function locales(): Collection;

    /**
     * @return list<string>
     */
    public function activeLocales(): array;

    public function defaultLocale(): string;

    public function createLocale(LocaleData $data): Locale;

    public function updateLocale(Locale $locale, LocaleData $data): Locale;

    public function deleteLocale(Locale $locale): void;

    // --- Translations ---

    public function translate(string $group, string $key, ?string $locale = null): ?string;

    public function setTranslation(string $group, string $key, string $locale, ?string $value): void;

    public function forgetTranslation(string $group, string $key, string $locale): void;

    /**
     * The translation map for a locale (optionally scoped to a group).
     *
     * @return array<string, string>
     */
    public function translations(string $locale, ?string $group = null): array;

    // --- Resolution ---

    /**
     * Resolve a locale-keyed value map through the fallback chain.
     *
     * @param  array<string, mixed>  $values
     */
    public function resolve(array $values, ?string $locale = null, ?string $context = null): mixed;

    // --- Slugs ---

    public function localizedSlug(string $subjectType, string $subjectId, ?string $locale = null): ?string;

    public function setSlug(string $subjectType, string $subjectId, string $locale, string $slug, bool $canonical = false): LocalizedSlug;

    public function uniqueSlug(string $subjectType, string $locale, string $base, ?int $ignoreId = null): string;

    // --- URLs ---

    public function url(string $path, ?string $locale = null): string;

    /**
     * Localized URLs for a path, keyed by locale code.
     *
     * @return array<string, string>
     */
    public function urlsForPath(string $path): array;
}
