<?php

declare(strict_types=1);

namespace App\Modules\Localization\Policies;

use App\Models\User;
use App\Modules\Localization\Models\Locale;

/**
 * Authorizes locale management actions against the `locales.*` permissions.
 * Super-admin bypass is owned by the Identity module's Gate::before hook and is
 * intentionally not re-registered here.
 */
final class LocalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('locales.view');
    }

    public function view(User $user, Locale $locale): bool
    {
        return $user->can('locales.view');
    }

    public function create(User $user): bool
    {
        return $user->can('locales.create');
    }

    public function update(User $user, Locale $locale): bool
    {
        return $user->can('locales.update');
    }

    public function delete(User $user, Locale $locale): bool
    {
        return $user->can('locales.delete');
    }
}
