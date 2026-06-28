<?php

declare(strict_types=1);

namespace App\Modules\Localization\Services;

use App\Modules\Localization\Contracts\LocaleRepositoryInterface;
use App\Modules\Localization\Contracts\LocalizationCacheInterface;
use App\Modules\Localization\Contracts\TranslationRepositoryInterface;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\Translation;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Collection;

/**
 * Caches the locale registry and per-locale translation maps so reads avoid the
 * database. Adds an in-request memo on top of the cache store so repeated reads
 * in one request touch neither the cache nor the database more than once.
 */
final class LocalizationCache implements LocalizationCacheInterface
{
    /** @var Collection<int, Locale>|null */
    private ?Collection $localeMemo = null;

    /** @var array<string, array<string, string>> */
    private array $translationMemo = [];

    public function __construct(
        private readonly LocaleRepositoryInterface $locales,
        private readonly TranslationRepositoryInterface $translations,
        private readonly CacheRepository $cache,
        private readonly string $cacheKey,
        private readonly int $ttl,
    ) {}

    /**
     * @return Collection<int, Locale>
     */
    public function locales(): Collection
    {
        if ($this->localeMemo !== null) {
            return $this->localeMemo;
        }

        $key = "{$this->cacheKey}.locales";

        $loader = fn (): Collection => $this->locales->all();

        return $this->localeMemo = $this->ttl > 0
            ? $this->cache->remember($key, $this->ttl, $loader)
            : $this->cache->rememberForever($key, $loader);
    }

    /**
     * @return array<string, string>
     */
    public function translations(string $locale): array
    {
        if (isset($this->translationMemo[$locale])) {
            return $this->translationMemo[$locale];
        }

        $key = $this->translationsKey($locale);

        $loader = fn (): array => $this->translations->forLocale($locale)
            ->mapWithKeys(static fn (Translation $translation): array => [
                "{$translation->group}.{$translation->key}" => (string) $translation->value,
            ])
            ->all();

        return $this->translationMemo[$locale] = $this->ttl > 0
            ? $this->cache->remember($key, $this->ttl, $loader)
            : $this->cache->rememberForever($key, $loader);
    }

    public function flush(): void
    {
        foreach ($this->knownLocaleCodes() as $code) {
            $this->cache->forget($this->translationsKey($code));
        }

        $this->cache->forget("{$this->cacheKey}.locales");

        $this->localeMemo = null;
        $this->translationMemo = [];
    }

    public function flushTranslations(?string $locale = null): void
    {
        if ($locale !== null) {
            $this->cache->forget($this->translationsKey($locale));
            unset($this->translationMemo[$locale]);

            return;
        }

        foreach ($this->knownLocaleCodes() as $code) {
            $this->cache->forget($this->translationsKey($code));
        }

        $this->translationMemo = [];
    }

    public function warm(): void
    {
        $this->flush();

        $locales = $this->locales();

        foreach ($locales as $locale) {
            if ($locale->is_active) {
                $this->translations($locale->code);
            }
        }
    }

    /**
     * The codes whose translation maps may currently be cached. Derived from the
     * (possibly cached) locale registry plus any locale touched in this request.
     *
     * @return list<string>
     */
    private function knownLocaleCodes(): array
    {
        $codes = $this->locales()
            ->map(static fn (Locale $locale): string => $locale->code)
            ->all();

        $codes = array_merge($codes, array_keys($this->translationMemo));

        return array_values(array_unique($codes));
    }

    private function translationsKey(string $locale): string
    {
        return "{$this->cacheKey}.translations.{$locale}";
    }
}
