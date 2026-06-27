<?php

declare(strict_types=1);

namespace App\Modules\Identity\Listeners;

use App\Core\Contracts\EventBusInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Events\UserLoggedOut;
use Illuminate\Auth\Events\Logout;

final class HandleLogout
{
    public function __construct(
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
    ) {}

    public function handle(Logout $event): void
    {
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        $this->events->dispatch(new UserLoggedOut($user));
        $this->activity->record('auth.logout', $user, 'User logged out');
    }
}
