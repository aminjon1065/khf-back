<?php

declare(strict_types=1);

namespace App\Modules\Localization\Facades;

use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\LocalizedSlug;
use App\Modules\Localization\Services\LocalizationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Static entry point to the Localization Engine.
 *
 * @method static Collection<int, Locale> locales()
 * @method static list<string> activeLocales()
 * @method static string defaultLocale()
 * @method static Locale createLocale(LocaleData $data)
 * @method static Locale updateLocale(Locale $locale, LocaleData $data)
 * @method static void deleteLocale(Locale $locale)
 * @method static string|null translate(string $group, string $key, ?string $locale = null)
 * @method static void setTranslation(string $group, string $key, string $locale, ?string $value)
 * @method static void forgetTranslation(string $group, string $key, string $locale)
 * @method static array<string, string> translations(string $locale, ?string $group = null)
 * @method static mixed resolve(array<string, mixed> $values, ?string $locale = null, ?string $context = null)
 * @method static string|null localizedSlug(string $subjectType, string $subjectId, ?string $locale = null)
 * @method static LocalizedSlug setSlug(string $subjectType, string $subjectId, string $locale, string $slug, bool $canonical = false)
 * @method static string uniqueSlug(string $subjectType, string $locale, string $base, ?int $ignoreId = null)
 * @method static string url(string $path, ?string $locale = null)
 * @method static array<string, string> urlsForPath(string $path)
 *
 * @see LocalizationService
 */
final class Localization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LocalizationServiceInterface::class;
    }
}
