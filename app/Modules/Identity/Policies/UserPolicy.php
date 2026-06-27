<?php

declare(strict_types=1);

namespace App\Modules\Identity\Policies;

use App\Models\User;
use App\Modules\Identity\Authorization\Permissions;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::USERS_VIEW);
    }

    public function view(User $user, User $model): bool
    {
        return $user->can(Permissions::USERS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::USERS_CREATE);
    }

    public function update(User $user, User $model): bool
    {
        return $user->can(Permissions::USERS_UPDATE);
    }

    public function delete(User $user, User $model): bool
    {
        // A user can never delete their own account through admin management.
        if ($user->is($model)) {
            return false;
        }

        return $user->can(Permissions::USERS_DELETE);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can(Permissions::USERS_DELETE);
    }
}
