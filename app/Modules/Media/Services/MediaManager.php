<?php

declare(strict_types=1);

namespace App\Modules\Media\Services;

use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\Contracts\StorageManagerInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Exceptions\MediaStorageException;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;

/**
 * Coordinates file-level operations (path layout, storing originals and
 * derivatives, deletion, temp copies) on top of the storage + imaging adapters.
 * It never reaches for Laravel Storage or Spatie directly.
 */
final class MediaManager
{
    public function __construct(
        private readonly StorageManagerInterface $storage,
        private readonly ImageProcessorInterface $imageProcessor,
    ) {}

    public function originalPath(string $mediaId, string $fileName): string
    {
        return "media/{$mediaId}/{$fileName}";
    }

    public function conversionPath(string $mediaId, string $conversionName, string $format): string
    {
        return "media/{$mediaId}/conversions/{$conversionName}.{$format}";
    }

    public function store(string $driver, string $disk, string $path, string $sourceAbsolutePath, MediaVisibility $visibility): void
    {
        $this->storage->driver($driver)->putFile($disk, $path, $sourceAbsolutePath, $visibility);
    }

    /**
     * Process a transformation from a local source, store the derivative and
     * persist a conversion record.
     */
    public function generateConversion(Media $media, string $sourceAbsolutePath, ImageTransformation $transformation): MediaConversion
    {
        $format = $transformation->format ?? ($media->extension ?? 'jpg');
        $tempDestination = $this->tempPath($format);

        try {
            $processed = $this->imageProcessor->convert($sourceAbsolutePath, $transformation, $tempDestination);
            $path = $this->conversionPath($media->id, $transformation->name, $processed->format);

            $this->storage->driver($media->driver)->putFile($media->disk, $path, $processed->absolutePath, $media->visibility);

            return MediaConversion::create([
                'media_id' => $media->id,
                'conversion_name' => $transformation->name,
                'driver' => $media->driver,
                'disk' => $media->disk,
                'path' => $path,
                'visibility' => $media->visibility, // a derivative inherits the original's visibility
                'mime_type' => $processed->mimeType,
                'format' => $processed->format,
                'width' => $processed->width,
                'height' => $processed->height,
                'size' => $processed->size,
            ]);
        } finally {
            if (is_file($tempDestination)) {
                @unlink($tempDestination);
            }
        }
    }

    public function deleteFiles(Media $media): void
    {
        $this->storage->driver($media->driver)->delete($media->disk, $media->path);

        foreach ($media->conversions as $conversion) {
            $this->storage->driver($conversion->driver)->delete($conversion->disk, $conversion->path);
        }
    }

    public function deleteStored(string $driver, string $disk, string $path): void
    {
        $this->storage->driver($driver)->delete($disk, $path);
    }

    /**
     * Materialise the stored original to a local temp file for re-processing,
     * streaming to avoid buffering the whole asset in memory.
     */
    public function copyOriginalToTemp(Media $media): string
    {
        $source = $this->storage->driver($media->driver)->readStream($media->disk, $media->path);
        $temp = $this->tempPath($media->extension ?? 'tmp');
        $destination = fopen($temp, 'wb');

        if ($destination === false) {
            fclose($source);

            throw MediaStorageException::writeFailed('temp', $temp);
        }

        try {
            stream_copy_to_stream($source, $destination);
        } finally {
            fclose($source);
            fclose($destination);
        }

        return $temp;
    }

    private function tempPath(string $extension): string
    {
        return sys_get_temp_dir().'/khf_media_'.bin2hex(random_bytes(8)).'.'.$extension;
    }
}
