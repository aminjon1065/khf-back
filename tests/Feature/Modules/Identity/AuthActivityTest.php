<?php

use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Events\UserLoggedIn;
use App\Modules\Identity\Events\UserLoggedOut;
use App\Modules\Identity\Models\Activity;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;

it('records an activity entry with captured context', function () {
    $user = User::factory()->create();

    $activity = app(ActivityLoggerInterface::class)
        ->record('test.event', $user, 'Did a thing', ['key' => 'value'], $user);

    expect($activity->type)->toBe('test.event')
        ->and($activity->user_id)->toBe($user->id)
        ->and($activity->properties)->toBe(['key' => 'value'])
        ->and($activity->subject_type)->toBe($user->getMorphClass());
    $this->assertDatabaseHas('activities', ['type' => 'test.event', 'user_id' => $user->id]);
});

it('logs login, updates last login and dispatches UserLoggedIn', function () {
    Event::fake([UserLoggedIn::class]);
    $user = User::factory()->create(['last_login_at' => null]);

    event(new Login('web', $user, false));

    $this->assertDatabaseHas('activities', ['type' => 'auth.login', 'user_id' => $user->id]);
    expect($user->fresh()->last_login_at)->not->toBeNull();
    Event::assertDispatched(UserLoggedIn::class);
});

it('logs logout and dispatches UserLoggedOut', function () {
    Event::fake([UserLoggedOut::class]);
    $user = User::factory()->create();

    event(new Logout('web', $user));

    $this->assertDatabaseHas('activities', ['type' => 'auth.logout', 'user_id' => $user->id]);
    Event::assertDispatched(UserLoggedOut::class);
});

it('logs failed login attempts without leaking the password', function () {
    event(new Failed('web', null, ['email' => 'attacker@example.com', 'password' => 'secret']));

    $activity = Activity::where('type', 'auth.failed')->firstOrFail();

    expect($activity->properties)->toBe(['email' => 'attacker@example.com']);
});

it('logs password resets', function () {
    $user = User::factory()->create();

    event(new PasswordReset($user));

    $this->assertDatabaseHas('activities', ['type' => 'auth.password_reset', 'user_id' => $user->id]);
});
