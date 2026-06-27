<?php

declare(strict_types=1);

namespace App\Modules\Identity\Repositories;

use App\Models\User;
use App\Modules\Identity\Contracts\UserRepositoryInterface;
use App\Modules\Identity\DTOs\CreateUserData;
use App\Modules\Identity\DTOs\UpdateUserData;
use App\Modules\Identity\Exceptions\UserNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findOrFail(int $id): User
    {
        $user = User::find($id);

        if ($user === null) {
            throw UserNotFoundException::withId($id);
        }

        return $user;
    }

    public function create(CreateUserData $data): User
    {
        $user = new User;
        $user->name = $data->name;
        $user->email = $data->email;
        $user->password = $data->password; // 'hashed' cast hashes on assignment
        $user->status = $data->status;
        $user->locale = $data->locale;
        $user->timezone = $data->timezone;
        $user->avatar_media_id = $data->avatarMediaId;
        $user->email_verified_at = now(); // operator-created accounts are pre-verified
        $user->save();

        if ($data->roles !== []) {
            $user->syncRoles($data->roles);
        }

        return $user;
    }

    public function update(User $user, UpdateUserData $data): User
    {
        if ($data->name !== null) {
            $user->name = $data->name;
        }
        if ($data->email !== null) {
            $user->email = $data->email;
        }
        if ($data->password !== null) {
            $user->password = $data->password;
        }
        if ($data->status !== null) {
            $user->status = $data->status;
        }
        if ($data->locale !== null) {
            $user->locale = $data->locale;
        }
        if ($data->timezone !== null) {
            $user->timezone = $data->timezone;
        }
        if ($data->avatarMediaId !== null) {
            $user->avatar_media_id = $data->avatarMediaId;
        }

        $user->save();

        if ($data->roles !== null) {
            $user->syncRoles($data->roles);
        }

        return $user;
    }

    public function delete(User $user, bool $permanently = false): void
    {
        if ($permanently) {
            $user->forceDelete();

            return;
        }

        $user->delete();
    }

    public function restore(User $user): User
    {
        $user->restore();

        return $user;
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return User::query()->orderBy('name')->paginate($perPage);
    }
}
