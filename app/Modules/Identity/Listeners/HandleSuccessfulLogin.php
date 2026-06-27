<?php

declare(strict_types=1);

namespace App\Modules\Identity\Listeners;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Events\UserLoggedIn;
use App\Modules\Identity\Support\IdentityHooks;
use Illuminate\Auth\Events\Login;

final class HandleSuccessfulLogin
{
    public function __construct(
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->save();

        $this->events->dispatch(new UserLoggedIn($user));
        $this->activity->record('auth.login', $user, 'User logged in');
        $this->hooks->doAction(IdentityHooks::AUTHENTICATED, $user);
    }
}
