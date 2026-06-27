<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\ProcessedImage;

/**
 * Image manipulation adapter. The default implementation wraps spatie/image and
 * spatie/image-optimizer; swapping it (e.g. for Intervention, libvips, or a
 * remote service) requires no change outside the Imaging namespace.
 */
interface ImageProcessorInterface
{
    public function isSupported(string $mimeType): bool;

    /**
     * Apply a transformation, writing the result to $destinationAbsolutePath.
     */
    public function convert(string $sourceAbsolutePath, ImageTransformation $transformation, string $destinationAbsolutePath): ProcessedImage;

    /**
     * Optimize a file in place (best-effort). Returns the resulting byte size.
     */
    public function optimize(string $absolutePath): int;
}
