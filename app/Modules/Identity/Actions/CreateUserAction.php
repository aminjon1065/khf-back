<?php

declare(strict_types=1);

namespace App\Modules\Identity\Actions;

use App\Core\Contracts\EventBusInterface;
use App\Models\User;
use App\Modules\Identity\Contracts\ActivityLoggerInterface;
use App\Modules\Identity\Contracts\UserRepositoryInterface;
use App\Modules\Identity\DTOs\CreateUserData;
use App\Modules\Identity\Events\UserCreated;
use App\Modules\Identity\Support\ResolvesActingUser;

final class CreateUserAction
{
    use ResolvesActingUser;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly EventBusInterface $events,
        private readonly ActivityLoggerInterface $activity,
    ) {}

    public function handle(CreateUserData $data): User
    {
        $user = $this->users->create($data);

        $this->events->dispatch(new UserCreated($user));
        $this->activity->record('user.created', $this->actingUser(), "Created user [{$user->email}]", ['email' => $user->email], $user);

        return $user;
    }
}
