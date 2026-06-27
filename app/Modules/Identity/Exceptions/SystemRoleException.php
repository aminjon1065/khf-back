<?php

declare(strict_types=1);

namespace App\Modules\Identity\Exceptions;

/**
 * Raised when a caller tries to mutate or delete a system (engine-owned) role.
 */
final class SystemRoleException extends IdentityException
{
    public static function cannotDelete(string $role): self
    {
        return new self("The system role [{$role}] cannot be deleted.");
    }
}
