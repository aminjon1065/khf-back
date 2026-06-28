<?php

declare(strict_types=1);

namespace App\Modules\Localization\Contracts;

use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Models\Locale;
use Illuminate\Support\Collection;

/**
 * Persistence boundary for locale records. Implementations are Eloquent-backed
 * and are the only place the engine touches the `locales` table directly.
 */
interface LocaleRepositoryInterface
{
    /**
     * Every locale, ordered for display.
     *
     * @return Collection<int, Locale>
     */
    public function all(): Collection;

    /**
     * Active locales only, ordered for display.
     *
     * @return Collection<int, Locale>
     */
    public function active(): Collection;

    public function find(string $code): ?Locale;

    public function findOrFail(string $code): Locale;

    public function default(): ?Locale;

    public function create(LocaleData $data): Locale;

    public function update(Locale $locale, LocaleData $data): Locale;

    public function delete(Locale $locale): void;

    /**
     * Unset the default flag on every locale except the given code.
     */
    public function clearDefaultExcept(string $code): void;
}
