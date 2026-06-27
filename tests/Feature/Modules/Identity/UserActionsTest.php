<?php

use App\Models\User;
use App\Modules\Identity\Actions\CreateUserAction;
use App\Modules\Identity\Actions\DeleteUserAction;
use App\Modules\Identity\Actions\UpdateUserAction;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\DTOs\CreateUserData;
use App\Modules\Identity\DTOs\UpdateUserData;
use App\Modules\Identity\Enums\UserStatus;
use App\Modules\Identity\Events\UserCreated;
use App\Modules\Identity\Events\UserDeleted;
use App\Modules\Identity\Events\UserUpdated;
use Database\Seeders\IdentityAccessSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(IdentityAccessSeeder::class);
});

it('creates a verified user with roles and dispatches UserCreated', function () {
    Event::fake([UserCreated::class]);

    $user = app(CreateUserAction::class)->handle(new CreateUserData(
        name: 'Jane Editor',
        email: 'jane@khf.tj',
        password: 'password123',
        roles: [Roles::EDITOR],
    ));

    expect($user->hasRole(Roles::EDITOR))->toBeTrue()
        ->and($user->hasVerifiedEmail())->toBeTrue()
        ->and(Hash::check('password123', $user->password))->toBeTrue();
    Event::assertDispatched(UserCreated::class);
    $this->assertDatabaseHas('activities', ['type' => 'user.created']);
});

it('updates a user and dispatches UserUpdated', function () {
    Event::fake([UserUpdated::class]);
    $user = User::factory()->create(['name' => 'Old']);

    $updated = app(UpdateUserAction::class)->handle($user, new UpdateUserData(
        name: 'New Name',
        status: UserStatus::Suspended,
    ));

    expect($updated->name)->toBe('New Name')
        ->and($updated->status)->toBe(UserStatus::Suspended);
    Event::assertDispatched(UserUpdated::class);
});

it('soft deletes a user and dispatches UserDeleted', function () {
    Event::fake([UserDeleted::class]);
    $user = User::factory()->create();

    app(DeleteUserAction::class)->handle($user);

    $this->assertSoftDeleted($user);
    Event::assertDispatched(UserDeleted::class, fn (UserDeleted $event): bool => $event->permanently === false);
});
