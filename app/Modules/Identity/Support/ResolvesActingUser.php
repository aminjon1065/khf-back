<?php

declare(strict_types=1);

namespace App\Modules\Identity\Support;

use App\Models\User;

trait ResolvesActingUser
{
    /**
     * The authenticated user performing the current action (null in console/system context).
     */
    protected function actingUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
