<?php

declare(strict_types=1);

namespace App\Modules\Identity\Actions;

use App\Core\Contracts\EventBusInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Contracts\UserRepositoryInterface;
use App\Modules\Identity\Events\UserDeleted;
use App\Modules\Identity\Support\ResolvesActingUser;

final class DeleteUserAction
{
    use ResolvesActingUser;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
    ) {}

    public function handle(User $user, bool $permanently = false): void
    {
        $email = $user->email;

        $this->users->delete($user, $permanently);

        $this->events->dispatch(new UserDeleted($user, $permanently));
        $this->activity->record('user.deleted', $this->actingUser(), "Deleted user [{$email}]", ['permanently' => $permanently], $user);
    }
}
