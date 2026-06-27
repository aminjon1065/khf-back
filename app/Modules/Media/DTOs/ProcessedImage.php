<?php

declare(strict_types=1);

namespace App\Modules\Media\DTOs;

/**
 * Result of running an image transformation: the produced file's intrinsic
 * properties. Storage concerns (disk/path) are added by the caller.
 */
final class ProcessedImage
{
    public function __construct(
        public readonly string $absolutePath,
        public readonly string $format,
        public readonly string $mimeType,
        public readonly int $width,
        public readonly int $height,
        public readonly int $size,
    ) {}
}
