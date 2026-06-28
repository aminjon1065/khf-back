<?php

declare(strict_types=1);

namespace App\Modules\Localization\Repositories;

use App\Modules\Localization\Contracts\TranslationRepositoryInterface;
use App\Modules\Localization\Models\Translation;
use Illuminate\Support\Collection;

/**
 * Eloquent-backed persistence for translation rows. The only place the engine
 * touches the `translations` table directly.
 */
final class EloquentTranslationRepository implements TranslationRepositoryInterface
{
    public function get(string $group, string $key, string $locale): ?Translation
    {
        return Translation::query()
            ->where('group', $group)
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();
    }

    public function put(string $group, string $key, string $locale, ?string $value): bool
    {
        $translation = Translation::query()->updateOrCreate(
            [
                'group' => $group,
                'key' => $key,
                'locale' => $locale,
            ],
            [
                'value' => $value,
            ],
        );

        return $translation->wasRecentlyCreated;
    }

    public function forget(string $group, string $key, string $locale): bool
    {
        return Translation::query()
            ->where('group', $group)
            ->where('key', $key)
            ->where('locale', $locale)
            ->delete() > 0;
    }

    /**
     * @return Collection<int, Translation>
     */
    public function forLocale(string $locale): Collection
    {
        return Translation::query()->forLocale($locale)->get();
    }

    /**
     * @return Collection<int, Translation>
     */
    public function forGroup(string $group, string $locale): Collection
    {
        return Translation::query()->inGroup($group)->forLocale($locale)->get();
    }
}
