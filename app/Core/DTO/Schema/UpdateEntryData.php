<?php

declare(strict_types=1);

namespace App\Core\DTO\Schema;

use App\Core\DTO\DataTransferObject;
use App\Core\Enums\EntryStatus;

/**
 * Partial-update payload for an existing entry. A null member means "leave
 * unchanged"; only non-null members are applied by the repository.
 */
final class UpdateEntryData extends DataTransferObject
{
    /**
     * @param  array<string, mixed>|null  $data
     */
    public function __construct(
        public readonly ?array $data = null,
        public readonly ?EntryStatus $status = null,
        public readonly ?string $slug = null,
        public readonly ?int $updatedBy = null,
    ) {}
}
