<?php

declare(strict_types=1);

namespace App\Modules\Identity\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
    case Invited = 'invited';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Suspended => 'Suspended',
            self::Invited => 'Invited',
        };
    }

    /**
     * Only active users may authenticate.
     */
    public function canAuthenticate(): bool
    {
        return $this === self::Active;
    }
}
