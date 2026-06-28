<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

use App\Modules\Localization\Models\Translation;
use Illuminate\Support\Collection;

/**
 * Persistence boundary for translation rows. Implementations are Eloquent-backed
 * and are the only place the engine touches the `translations` table directly.
 */
interface TranslationRepositoryInterface
{
    public function get(string $group, string $key, string $locale): ?Translation;

    /**
     * Persist a translation value. Returns true if the row was newly created.
     */
    public function put(string $group, string $key, string $locale, ?string $value): bool;

    public function forget(string $group, string $key, string $locale): bool;

    /**
     * All translations for a locale.
     *
     * @return Collection<int, Translation>
     */
    public function forLocale(string $locale): Collection;

    /**
     * All translations for a group in a locale.
     *
     * @return Collection<int, Translation>
     */
    public function forGroup(string $group, string $locale): Collection;
}
