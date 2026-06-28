<?php

declare(strict_types=1);

namespace App\Modules\Localization\Repositories;

use App\Modules\Localization\Contracts\LocaleRepositoryInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Exceptions\LocaleNotFoundException;
use App\Modules\Localization\Models\Locale;
use Illuminate\Support\Collection;

/**
 * Eloquent-backed persistence for locale records. The only place the engine
 * touches the `locales` table directly.
 */
final class EloquentLocaleRepository implements LocaleRepositoryInterface
{
    /**
     * @return Collection<int, Locale>
     */
    public function all(): Collection
    {
        return Locale::query()->ordered()->get();
    }

    /**
     * @return Collection<int, Locale>
     */
    public function active(): Collection
    {
        return Locale::query()->active()->ordered()->get();
    }

    public function find(string $code): ?Locale
    {
        return Locale::query()->where('code', $code)->first();
    }

    public function findOrFail(string $code): Locale
    {
        $locale = $this->find($code);

        if ($locale === null) {
            throw LocaleNotFoundException::code($code);
        }

        return $locale;
    }

    public function default(): ?Locale
    {
        return Locale::query()->where('is_default', true)->first();
    }

    public function create(LocaleData $data): Locale
    {
        return Locale::create($data->toAttributes());
    }

    public function update(Locale $locale, LocaleData $data): Locale
    {
        $locale->update($data->toAttributes());

        return $locale->refresh();
    }

    public function delete(Locale $locale): void
    {
        $locale->delete();
    }

    public function clearDefaultExcept(string $code): void
    {
        Locale::query()
            ->where('code', '!=', $code)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
