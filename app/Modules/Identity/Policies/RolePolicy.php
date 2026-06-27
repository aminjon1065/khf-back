<?php

declare(strict_types=1);

namespace App\Modules\Identity\Policies;

use App\Models\User;
use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Models\Role;

final class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::ROLES_MANAGE);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can(Permissions::ROLES_MANAGE);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::ROLES_MANAGE);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can(Permissions::ROLES_MANAGE);
    }

    public function delete(User $user, Role $role): bool
    {
        // System roles are engine-owned and may never be deleted.
        if ($role->isSystem()) {
            return false;
        }

        return $user->can(Permissions::ROLES_MANAGE);
    }
}
