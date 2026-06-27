<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Contracts\PermissionRegistryInterface;
use App\Modules\Identity\Models\Permission;
use App\Modules\Identity\Models\Role;
use App\Modules\Identity\Support\IdentityHooks;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the canonical IAM permission catalogue and the seven system roles.
 * Additive and idempotent — it leaves legacy permissions/roles untouched.
 */
class IdentityAccessSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permissions::catalog() as $definition) {
            $permission = Permission::firstOrCreate(['name' => $definition['name'], 'guard_name' => 'web']);
            $permission->description = $definition['description'];
            $permission->category = $definition['category'];
            $permission->save();
        }

        // Seed any plugin-registered permissions (added via the REGISTER_PERMISSIONS hook).
        foreach (app(PermissionRegistryInterface::class)->all() as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdmin = Role::firstOrCreate(['name' => Roles::SUPER_ADMIN, 'guard_name' => 'web']);
        $superAdmin->is_system = true;
        $superAdmin->description = 'Full, unrestricted access (wildcard).';
        $superAdmin->save();
        // No explicit permissions: Super Admin is all-powerful via Gate::before.

        foreach ($this->roleDefinitions() as $name => $permissions) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $role->is_system = true;
            $role->description = ucfirst($name).' system role.';
            $role->save();
            // Additive: 'editor' is shared with the legacy role, so granting (not
            // syncing) preserves its existing 'manage *' permissions.
            $role->givePermissionTo($permissions);
        }

        $this->bridgeLegacyRoles();
    }

    /**
     * Canonical role definitions, with plugin additions applied via the
     * REGISTER_ROLES hook.
     *
     * @return array<string, list<string>>
     */
    private function roleDefinitions(): array
    {
        $filtered = app(HookManagerInterface::class)
            ->applyFilters(IdentityHooks::REGISTER_ROLES, Roles::definitions());

        if (! is_array($filtered)) {
            return Roles::definitions();
        }

        $definitions = [];
        foreach ($filtered as $name => $permissions) {
            if (is_string($name) && is_array($permissions)) {
                $definitions[$name] = array_values(array_map(strval(...), $permissions));
            }
        }

        return $definitions;
    }

    /**
     * Grant the pre-existing legacy 'admin' role the full granular catalogue so
     * the new policies authorize it without disturbing its 'manage *' permissions.
     */
    private function bridgeLegacyRoles(): void
    {
        $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        $admin?->givePermissionTo(Permissions::all());
    }
}
