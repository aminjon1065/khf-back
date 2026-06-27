<?php

declare(strict_types=1);

namespace App\Modules\Identity\Listeners;

use App\Core\Contracts\EventBusInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Events\UserCreated;
use Illuminate\Auth\Events\Registered;

/**
 * Bridges Fortify's self-registration into the IAM event stream so a registered
 * user produces the same UserCreated event + activity entry as an admin-created
 * one (which goes through CreateUserAction).
 */
final class HandleRegistered
{
    public function __construct(
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
    ) {}

    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        $this->events->dispatch(new UserCreated($user));
        $this->activity->record('user.registered', $user, "User registered [{$user->email}]", [], $user);
    }
}
