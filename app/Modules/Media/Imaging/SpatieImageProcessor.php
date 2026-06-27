<?php

declare(strict_types=1);

namespace App\Modules\Media\Imaging;

use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\ProcessedImage;
use App\Modules\Media\Enums\ImageFit;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Throwable;

/**
 * The Media Engine's image adapter. This is the ONLY class permitted to import
 * Spatie; replacing the image stack means swapping this single binding.
 */
final class SpatieImageProcessor implements ImageProcessorInterface
{
    private const SUPPORTED = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/avif',
        'image/bmp',
        'image/tiff',
    ];

    public function __construct(private readonly string $driver = 'imagick') {}

    public function isSupported(string $mimeType): bool
    {
        return in_array($mimeType, self::SUPPORTED, true);
    }

    public function convert(string $sourceAbsolutePath, ImageTransformation $transformation, string $destinationAbsolutePath): ProcessedImage
    {
        $image = Image::useImageDriver($this->driver)->loadFile($sourceAbsolutePath);

        if ($transformation->rotation !== null) {
            $orientation = Orientation::tryFrom($transformation->rotation);

            if ($orientation !== null) {
                $image->orientation($orientation);
            }
        }

        if ($transformation->width !== null && $transformation->height !== null) {
            $image->fit($this->mapFit($transformation->fit), $transformation->width, $transformation->height);
        } elseif ($transformation->width !== null) {
            $image->width($transformation->width);
        } elseif ($transformation->height !== null) {
            $image->height($transformation->height);
        }

        $image->quality($transformation->quality);

        if ($transformation->format !== null) {
            $image->format($transformation->format);
        }

        $image->save($destinationAbsolutePath);

        if ($transformation->optimize) {
            $this->optimize($destinationAbsolutePath);
        }

        return $this->describe($destinationAbsolutePath, $transformation->format);
    }

    public function optimize(string $absolutePath): int
    {
        try {
            OptimizerChainFactory::create()->optimize($absolutePath);
        } catch (Throwable) {
            // Optimization is best-effort: a missing binary must never fail an upload.
        }

        clearstatcache(true, $absolutePath);

        return (int) filesize($absolutePath);
    }

    private function describe(string $path, ?string $requestedFormat): ProcessedImage
    {
        $probe = Image::useImageDriver($this->driver)->loadFile($path);
        $format = $requestedFormat ?? strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return new ProcessedImage(
            absolutePath: $path,
            format: $format,
            mimeType: $this->mimeForFormat($format, $path),
            width: $probe->getWidth(),
            height: $probe->getHeight(),
            size: (int) filesize($path),
        );
    }

    private function mapFit(ImageFit $fit): Fit
    {
        return match ($fit) {
            ImageFit::Contain => Fit::Contain,
            ImageFit::Crop => Fit::Crop,
            ImageFit::Fill => Fit::Fill,
            ImageFit::Max => Fit::Max,
            ImageFit::Stretch => Fit::Stretch,
        };
    }

    private function mimeForFormat(string $format, string $path): string
    {
        return match (strtolower($format)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff',
            default => mime_content_type($path) ?: 'application/octet-stream',
        };
    }
}
