<?php

declare(strict_types=1);

namespace App\Modules\Identity\Services;

use App\Core\Contracts\EventBusInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Contracts\IdentityServiceInterface;
use App\Modules\Identity\Events\PermissionGranted;
use App\Modules\Identity\Events\PermissionRevoked;
use App\Modules\Identity\Events\RoleAssigned;
use App\Modules\Identity\Exceptions\SystemRoleException;
use App\Modules\Identity\Models\Role;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

final class IdentityService implements IdentityServiceInterface
{
    public function __construct(
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
    ) {}

    public function can(User $user, string $permission): bool
    {
        // Gate-based: honours the Super Admin wildcard and policies.
        return $user->can($permission);
    }

    public function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    public function hasPermission(User $user, string $permission): bool
    {
        // Literal assignment check (ignores the Super Admin wildcard).
        try {
            return $user->hasPermissionTo($permission);
        } catch (PermissionDoesNotExist) {
            return false;
        }
    }

    public function assignRole(User $user, string $role): User
    {
        $user->assignRole($role);

        $this->events->dispatch(new RoleAssigned($user, $role));
        $this->activity->record('role.assigned', $user, "Assigned role [{$role}]", ['role' => $role], $user);

        return $user;
    }

    public function removeRole(User $user, string $role): User
    {
        $user->removeRole($role);

        $this->activity->record('role.removed', $user, "Removed role [{$role}]", ['role' => $role], $user);

        return $user;
    }

    /**
     * @param  list<string>  $roles
     */
    public function syncRoles(User $user, array $roles): User
    {
        $user->syncRoles($roles);

        $this->activity->record('role.synced', $user, 'Synced roles', ['roles' => $roles], $user);

        return $user;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function syncPermissions(User $user, array $permissions): User
    {
        $user->syncPermissions($permissions);

        $this->activity->record('permission.synced', $user, 'Synced permissions', ['permissions' => $permissions], $user);

        return $user;
    }

    public function grantPermission(User $user, string $permission): User
    {
        $user->givePermissionTo($permission);

        $this->events->dispatch(new PermissionGranted($user, $permission));
        $this->activity->record('permission.granted', $user, "Granted [{$permission}]", ['permission' => $permission], $user);

        return $user;
    }

    public function revokePermission(User $user, string $permission): User
    {
        $user->revokePermissionTo($permission);

        $this->events->dispatch(new PermissionRevoked($user, $permission));
        $this->activity->record('permission.revoked', $user, "Revoked [{$permission}]", ['permission' => $permission], $user);

        return $user;
    }

    /**
     * @param  list<string>  $permissions
     */
    public function createRole(string $name, array $permissions = [], ?string $description = null): Role
    {
        $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        $role->description = $description;
        $role->is_system = false;
        $role->save();

        if ($permissions !== []) {
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        if ($role->isSystem()) {
            throw SystemRoleException::cannotDelete($role->name);
        }

        $role->delete();
    }
}
