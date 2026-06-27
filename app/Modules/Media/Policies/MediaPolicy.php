<?php

declare(strict_types=1);

namespace App\Modules\Media\Policies;

use App\Models\User;
use App\Modules\Media\Models\Media;

final class MediaPolicy
{
    private const PERMISSION = 'manage media';

    public function viewAny(User $user): bool
    {
        return $user->can(self::PERMISSION);
    }

    public function view(User $user, Media $media): bool
    {
        return $user->can(self::PERMISSION);
    }

    public function create(User $user): bool
    {
        return $user->can(self::PERMISSION);
    }

    public function update(User $user, Media $media): bool
    {
        return $user->can(self::PERMISSION);
    }

    public function delete(User $user, Media $media): bool
    {
        return $user->can(self::PERMISSION);
    }

    public function restore(User $user, Media $media): bool
    {
        return $user->can(self::PERMISSION);
    }

    public function forceDelete(User $user, Media $media): bool
    {
        return $user->can(self::PERMISSION);
    }
}
