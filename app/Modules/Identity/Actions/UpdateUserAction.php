<?php

declare(strict_types=1);

namespace App\Modules\Identity\Actions;

use App\Core\Contracts\EventBusInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Contracts\UserRepositoryInterface;
use App\Modules\Identity\DTOs\UpdateUserData;
use App\Modules\Identity\Events\UserUpdated;
use App\Modules\Identity\Support\ResolvesActingUser;

final class UpdateUserAction
{
    use ResolvesActingUser;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
    ) {}

    public function handle(User $user, UpdateUserData $data): User
    {
        $user = $this->users->update($user, $data);

        $this->events->dispatch(new UserUpdated($user));
        $this->activity->record('user.updated', $this->actingUser(), "Updated user [{$user->email}]", [], $user);

        return $user;
    }
}
