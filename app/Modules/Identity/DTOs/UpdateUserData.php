<?php

declare(strict_types=1);

namespace App\Modules\Identity\DTOs;

use App\Modules\Identity\Enums\UserStatus;

/**
 * Partial user update — a null member means "leave unchanged". A null $roles
 * leaves role assignments untouched; an empty array clears them.
 */
final class UpdateUserData
{
    /**
     * @param  list<string>|null  $roles
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?UserStatus $status = null,
        public readonly ?string $locale = null,
        public readonly ?string $timezone = null,
        public readonly ?string $avatarMediaId = null,
        public readonly ?array $roles = null,
    ) {}
}
