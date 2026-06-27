<?php

declare(strict_types=1);

namespace App\Modules\Identity\Exceptions;

final class UserNotFoundException extends IdentityException
{
    public static function withId(int $id): self
    {
        return new self("No user found with id [{$id}].");
    }
}
