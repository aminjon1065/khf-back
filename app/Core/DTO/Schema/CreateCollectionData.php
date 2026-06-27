<?php

declare(strict_types=1);

namespace App\Core\DTO\Schema;

use App\Core\DTO\DataTransferObject;

final class CreateCollectionData extends DataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
        public readonly ?string $icon = null,
    ) {}
}
