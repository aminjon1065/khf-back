<?php

use App\Models\User;
use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Contracts\IdentityServiceInterface;
use App\Modules\Identity\Events\PermissionGranted;
use App\Modules\Identity\Events\PermissionRevoked;
use App\Modules\Identity\Events\RoleAssigned;
use App\Modules\Identity\Exceptions\SystemRoleException;
use App\Modules\Identity\Models\Role;
use Database\Seeders\IdentityAccessSeeder;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->seed(IdentityAccessSeeder::class);
});

it('assigns and removes roles, dispatching RoleAssigned', function () {
    Event::fake([RoleAssigned::class]);
    $service = app(IdentityServiceInterface::class);
    $user = User::factory()->create();

    $service->assignRole($user, Roles::EDITOR);
    expect($service->hasRole($user, Roles::EDITOR))->toBeTrue();
    Event::assertDispatched(RoleAssigned::class);

    $service->removeRole($user, Roles::EDITOR);
    expect($service->hasRole($user, Roles::EDITOR))->toBeFalse();
});

it('grants and revokes permissions with events and activity', function () {
    Event::fake([PermissionGranted::class, PermissionRevoked::class]);
    $service = app(IdentityServiceInterface::class);
    $user = User::factory()->create();

    $service->grantPermission($user, Permissions::ENTRIES_CREATE);
    expect($service->hasPermission($user, Permissions::ENTRIES_CREATE))->toBeTrue();
    Event::assertDispatched(PermissionGranted::class);
    $this->assertDatabaseHas('activities', ['type' => 'permission.granted']);

    $service->revokePermission($user, Permissions::ENTRIES_CREATE);
    expect($service->hasPermission($user, Permissions::ENTRIES_CREATE))->toBeFalse();
    Event::assertDispatched(PermissionRevoked::class);
});

it('grants the Super Admin every ability through the wildcard', function () {
    $service = app(IdentityServiceInterface::class);
    $user = User::factory()->create();
    $service->assignRole($user, Roles::SUPER_ADMIN);

    expect($service->can($user, Permissions::SETTINGS_MANAGE))->toBeTrue()
        ->and($service->can($user, 'some.permission.that.does.not.exist'))->toBeTrue()
        // hasPermission ignores the wildcard — it reflects literal assignment.
        ->and($service->hasPermission($user, Permissions::SETTINGS_MANAGE))->toBeFalse();
});

it('creates a custom (non-system) role with permissions', function () {
    $role = app(IdentityServiceInterface::class)->createRole('content-team', [Permissions::ENTRIES_VIEW], 'Custom role');

    expect($role->isSystem())->toBeFalse()
        ->and($role->description)->toBe('Custom role')
        ->and($role->hasPermissionTo(Permissions::ENTRIES_VIEW))->toBeTrue();
});

it('refuses to delete a system role', function () {
    $system = Role::where('name', Roles::ADMINISTRATOR)->firstOrFail();

    app(IdentityServiceInterface::class)->deleteRole($system);
})->throws(SystemRoleException::class);

it('deletes a custom role', function () {
    $service = app(IdentityServiceInterface::class);
    $role = $service->createRole('temporary', []);

    $service->deleteRole($role);

    $this->assertDatabaseMissing('roles', ['name' => 'temporary']);
});
