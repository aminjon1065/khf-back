<?php

declare(strict_types=1);

namespace App\Modules\Identity\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

final class PermissionRevoked
{
    use Dispatchable;

    public function __construct(
        public readonly User $user,
        public readonly string $permission,
    ) {}
}
