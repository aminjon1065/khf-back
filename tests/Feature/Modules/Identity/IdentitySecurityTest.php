<?php

use App\Models\User;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Events\UserCreated;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Event;

it('blocks a suspended user from authenticating even with valid credentials', function () {
    $user = User::factory()->suspended()->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password']);

    $this->assertGuest();
});

it('allows an active user to authenticate', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password']);

    $this->assertAuthenticated();
});

it('prevents a non-super-admin from assigning the super-admin role', function () {
    $this->withoutVite();
    $this->seed(RolesAndPermissionsSeeder::class);
    $admin = User::where('email', 'admin@khf.tj')->firstOrFail();

    $this->actingAs($admin)
        ->post('/admin/users', [
            'name' => 'Escalator',
            'email' => 'escalate@khf.tj',
            'password' => 'password123',
            'role' => Roles::SUPER_ADMIN,
        ])
        ->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('users', ['email' => 'escalate@khf.tj']);
});

it('lets a super-admin assign the super-admin role and reach the admin shell', function () {
    $this->withoutVite();
    $this->seed(RolesAndPermissionsSeeder::class);
    $super = User::factory()->create();
    $super->assignRole(Roles::SUPER_ADMIN);

    $this->actingAs($super)
        ->post('/admin/users', [
            'name' => 'Trusted',
            'email' => 'trusted@khf.tj',
            'password' => 'password123',
            'role' => Roles::SUPER_ADMIN,
        ])
        ->assertRedirect('/admin/users');

    expect(User::where('email', 'trusted@khf.tj')->firstOrFail()->hasRole(Roles::SUPER_ADMIN))->toBeTrue();
});

it('dispatches UserCreated and logs activity when a user self-registers', function () {
    Event::fake([UserCreated::class]);

    $this->post(route('register.store'), [
        'name' => 'New Person',
        'email' => 'newperson@khf.tj',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect();

    Event::assertDispatched(UserCreated::class);
    $this->assertDatabaseHas('activities', ['type' => 'user.registered']);
});
