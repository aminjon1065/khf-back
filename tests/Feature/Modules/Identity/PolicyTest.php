<?php

use App\Models\User;
use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Contracts\IdentityServiceInterface;
use App\Modules\Identity\Models\Role;
use Database\Seeders\IdentityAccessSeeder;

beforeEach(function () {
    $this->seed(IdentityAccessSeeder::class);
});

it('authorizes user viewing/creating by granular permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([Permissions::USERS_VIEW, Permissions::USERS_CREATE]);

    expect($user->can('viewAny', User::class))->toBeTrue()
        ->and($user->can('create', User::class))->toBeTrue();

    $stranger = User::factory()->create();
    expect($stranger->can('viewAny', User::class))->toBeFalse();
});

it('forbids deleting your own account but allows deleting others', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permissions::USERS_DELETE);
    $other = User::factory()->create();

    expect($user->can('delete', $user))->toBeFalse()
        ->and($user->can('delete', $other))->toBeTrue();
});

it('forbids deleting a system role but allows deleting a custom role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permissions::ROLES_MANAGE);

    $systemRole = Role::where('name', Roles::EDITOR)->firstOrFail();
    $customRole = app(IdentityServiceInterface::class)->createRole('temp-role', []);

    expect($user->can('delete', $systemRole))->toBeFalse()
        ->and($user->can('delete', $customRole))->toBeTrue();
});

it('lets a Super Admin bypass every policy', function () {
    $user = User::factory()->create();
    $user->assignRole(Roles::SUPER_ADMIN);
    $systemRole = Role::where('name', Roles::EDITOR)->firstOrFail();

    expect($user->can('viewAny', User::class))->toBeTrue()
        ->and($user->can('create', User::class))->toBeTrue()
        // The wildcard even overrides the system-role delete guard.
        ->and($user->can('delete', $systemRole))->toBeTrue();
});
