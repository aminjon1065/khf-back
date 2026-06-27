<?php

declare(strict_types=1);

namespace App\Modules\Media\Pipeline;

use App\Modules\Media\DTOs\ExtractedMetadata;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Mutable state threaded through the upload pipeline. Each stage reads what it
 * needs and writes its results back here.
 */
final class UploadContext
{
    public ?string $checksum = null;

    public ExtractedMetadata $metadata;

    public ?string $storedPath = null;

    public int $originalSize;

    public int $optimizedSize;

    public ?Media $media = null;

    /** @var list<MediaConversion> */
    public array $generatedConversions = [];

    /**
     * @param  array<string, mixed>  $customProperties
     * @param  list<ImageTransformation>|null  $requestedConversions
     */
    public function __construct(
        public readonly File $file,
        public readonly string $mediaId,
        public readonly string $sourcePath,
        public readonly string $originalFileName,
        public string $fileName,
        public readonly string $extension,
        public string $mimeType,
        public int $size,
        public readonly string $disk,
        public readonly string $driver,
        public readonly MediaVisibility $visibility,
        public readonly ?int $uploadedBy,
        public ?string $name,
        public ?string $altText,
        public ?string $caption,
        public ?string $copyright,
        public array $customProperties,
        public readonly ?array $requestedConversions,
    ) {
        $this->metadata = ExtractedMetadata::empty();
        $this->originalSize = $size;
        $this->optimizedSize = $size;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }
}
