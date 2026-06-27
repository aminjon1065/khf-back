<?php

declare(strict_types=1);

namespace App\Modules\Media\DTOs;

/**
 * Editable descriptive metadata for an existing asset. A null member means
 * "leave unchanged".
 */
final class UpdateMetadataData
{
    /**
     * @param  array{x: float, y: float}|null  $focalPoint
     * @param  array<string, mixed>|null  $customProperties
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $altText = null,
        public readonly ?string $caption = null,
        public readonly ?string $copyright = null,
        public readonly ?array $focalPoint = null,
        public readonly ?array $customProperties = null,
    ) {}
}
