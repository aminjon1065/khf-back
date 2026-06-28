<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

use App\Modules\Localization\Models\Locale;
use Illuminate\Support\Collection;

/**
 * Caches the locale registry and per-locale translation maps so reads avoid the
 * database. Lazily warmed on first access; invalidated on every write.
 */
interface LocalizationCacheInterface
{
    /**
     * The cached locale registry (lazily warmed from the repository).
     *
     * @return Collection<int, Locale>
     */
    public function locales(): Collection;

    /**
     * The cached translation map for a locale, keyed by "group.key".
     *
     * @return array<string, string>
     */
    public function translations(string $locale): array;

    /**
     * Invalidate the entire cache so the next read re-warms it.
     */
    public function flush(): void;

    /**
     * Invalidate one locale's translation map, or all of them when null.
     */
    public function flushTranslations(?string $locale = null): void;

    /**
     * Eagerly (re)build the cache from the repository.
     */
    public function warm(): void;
}
