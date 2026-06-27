<?php

declare(strict_types=1);

namespace App\Modules\Identity\Contracts;

use App\Models\User;
use App\Modules\Identity\Models\Role;

/**
 * The authorization façade of the Identity module. Every module asks identity
 * questions and performs role/permission changes exclusively through this
 * contract — no module touches Spatie directly or hardcodes role checks.
 */
interface IdentityServiceInterface
{
    public function can(User $user, string $permission): bool;

    public function hasRole(User $user, string $role): bool;

    public function hasPermission(User $user, string $permission): bool;

    public function assignRole(User $user, string $role): User;

    public function removeRole(User $user, string $role): User;

    /**
     * @param  list<string>  $roles
     */
    public function syncRoles(User $user, array $roles): User;

    /**
     * @param  list<string>  $permissions
     */
    public function syncPermissions(User $user, array $permissions): User;

    public function grantPermission(User $user, string $permission): User;

    public function revokePermission(User $user, string $permission): User;

    /**
     * @param  list<string>  $permissions
     */
    public function createRole(string $name, array $permissions = [], ?string $description = null): Role;

    public function deleteRole(Role $role): void;
}
