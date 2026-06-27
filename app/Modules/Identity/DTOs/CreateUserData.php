<?php

declare(strict_types=1);

namespace App\Modules\Identity\DTOs;

use App\Modules\Identity\Enums\UserStatus;

final class CreateUserData
{
    /**
     * @param  list<string>  $roles
     */
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly UserStatus $status = UserStatus::Active,
        public readonly ?string $locale = null,
        public readonly ?string $timezone = null,
        public readonly ?string $avatarMediaId = null,
        public readonly array $roles = [],
    ) {}
}
