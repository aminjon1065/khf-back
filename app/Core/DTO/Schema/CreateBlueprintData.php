<?php

declare(strict_types=1);

namespace App\Core\DTO\Schema;

use App\Core\DTO\DataTransferObject;

final class CreateBlueprintData extends DataTransferObject
{
    public function __construct(
        public readonly string $collectionId,
        public readonly string $name,
    ) {}
}
