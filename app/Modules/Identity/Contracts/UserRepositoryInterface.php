<?php

declare(strict_types=1);

namespace App\Modules\Identity\Contracts;

use App\Models\User;
use App\Modules\Identity\DTOs\CreateUserData;
use App\Modules\Identity\DTOs\UpdateUserData;
use App\Modules\Identity\Exceptions\UserNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function find(int $id): ?User;

    public function findByUuid(string $uuid): ?User;

    public function findByEmail(string $email): ?User;

    /**
     * @throws UserNotFoundException
     */
    public function findOrFail(int $id): User;

    public function create(CreateUserData $data): User;

    public function update(User $user, UpdateUserData $data): User;

    public function delete(User $user, bool $permanently = false): void;

    public function restore(User $user): User;

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator;
}
