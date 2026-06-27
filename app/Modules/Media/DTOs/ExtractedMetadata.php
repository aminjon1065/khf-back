<?php

declare(strict_types=1);

namespace App\Modules\Media\DTOs;

/**
 * Intrinsic, machine-extracted metadata for a file (as opposed to the editable
 * descriptive metadata a human supplies).
 */
final class ExtractedMetadata
{
    /**
     * @param  array<string, mixed>  $exif
     */
    public function __construct(
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?int $duration = null,
        public readonly ?string $dominantColor = null,
        public readonly array $exif = [],
    ) {}

    public static function empty(): self
    {
        return new self;
    }
}
