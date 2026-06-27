<?php

declare(strict_types=1);

namespace App\Modules\Media\DTOs;

use App\Modules\Media\Models\Media;

/**
 * Framework-agnostic representation of a media asset for cross-module exchange.
 * This is what other modules receive — never the Eloquent model internals nor
 * any Spatie type.
 */
final class MediaData
{
    /**
     * @param  array{x: float, y: float}|null  $focalPoint
     * @param  list<array{name: string, format: string, mime_type: string, width: int|null, height: int|null, size: int}>  $conversions
     */
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly ?int $width,
        public readonly ?int $height,
        public readonly ?int $duration,
        public readonly string $fileName,
        public readonly string $originalFileName,
        public readonly string $visibility,
        public readonly string $disk,
        public readonly string $url,
        public readonly ?string $name,
        public readonly ?string $altText,
        public readonly ?string $caption,
        public readonly ?string $copyright,
        public readonly ?array $focalPoint,
        public readonly ?string $dominantColor,
        public readonly ?string $checksum,
        public readonly array $conversions = [],
    ) {}

    public static function fromModel(Media $media, string $url): self
    {
        $conversions = [];

        if ($media->relationLoaded('conversions')) {
            foreach ($media->conversions as $conversion) {
                $conversions[] = [
                    'name' => $conversion->conversion_name,
                    'format' => $conversion->format,
                    'mime_type' => $conversion->mime_type,
                    'width' => $conversion->width,
                    'height' => $conversion->height,
                    'size' => $conversion->size,
                ];
            }
        }

        return new self(
            id: $media->id,
            type: $media->type()->value,
            mimeType: $media->mime_type,
            size: $media->size,
            width: $media->width,
            height: $media->height,
            duration: $media->duration,
            fileName: $media->file_name,
            originalFileName: $media->original_file_name,
            visibility: $media->visibility->value,
            disk: $media->disk,
            url: $url,
            name: $media->name,
            altText: $media->alt_text,
            caption: $media->caption,
            copyright: $media->copyright,
            focalPoint: $media->focal_point,
            dominantColor: $media->dominant_color,
            checksum: $media->checksum,
            conversions: $conversions,
        );
    }
}
