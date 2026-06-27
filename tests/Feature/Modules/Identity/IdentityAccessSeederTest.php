<?php

use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Models\Role;
use Database\Seeders\IdentityAccessSeeder;

beforeEach(function () {
    $this->seed(IdentityAccessSeeder::class);
});

it('seeds the full canonical permission catalogue', function () {
    foreach (Permissions::all() as $permission) {
        $this->assertDatabaseHas('permissions', ['name' => $permission, 'guard_name' => 'web']);
    }
});

it('seeds all seven system roles flagged is_system', function () {
    foreach (Roles::systemRoles() as $name) {
        $role = Role::where('name', $name)->first();

        expect($role)->not->toBeNull()
            ->and($role->isSystem())->toBeTrue();
    }
});

it('grants each system role its defined permissions', function () {
    $administrator = Role::where('name', Roles::ADMINISTRATOR)->firstOrFail();
    $viewer = Role::where('name', Roles::VIEWER)->firstOrFail();

    expect($administrator->hasPermissionTo(Permissions::USERS_CREATE))->toBeTrue()
        ->and($viewer->hasPermissionTo(Permissions::ENTRIES_VIEW))->toBeTrue()
        ->and($viewer->hasPermissionTo(Permissions::ENTRIES_DELETE))->toBeFalse();
});
