<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /** @var list<string> $permissions */
        $permissions = [
            'view dashboard',
            'manage news',
            'manage documents',
            'manage structure',
            'manage activities',
            'manage forum',
            'manage regions',
            'manage contacts',
            'manage home',
            'publish content',
            'manage media',
            'manage submissions',
            'manage settings',
            'manage users',
            'manage roles',
        ];

        $permissionModels = collect($permissions)
            ->mapWithKeys(fn (string $name): array => [$name => Permission::findOrCreate($name, 'web')]);

        // Сбрасываем кеш после создания, чтобы sync находил права по имени.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($permissionModels->values());

        // Редактор: весь контент, но без управления настройками/пользователями/ролями.
        $adminOnly = ['manage settings', 'manage users', 'manage roles'];
        $editor = Role::findOrCreate('editor', 'web');
        $editor->syncPermissions(
            $permissionModels->except($adminOnly)->values(),
        );

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@khf.tj'],
            ['name' => 'Администратор', 'password' => Hash::make('password'), 'email_verified_at' => now()],
        );
        $adminUser->syncRoles([$admin]);

        $editorUser = User::firstOrCreate(
            ['email' => 'editor@khf.tj'],
            ['name' => 'Редактор', 'password' => Hash::make('password'), 'email_verified_at' => now()],
        );
        $editorUser->syncRoles([$editor]);
    }
}
