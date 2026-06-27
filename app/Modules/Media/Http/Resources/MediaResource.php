<?php

declare(strict_types=1);

namespace App\Modules\Media\Http\Resources;

use App\Modules\Media\Contracts\UrlGeneratorInterface;
use App\Modules\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * HTTP serialization of a media asset. Exposes only the engine's own data — no
 * Spatie or storage internals leak through.
 *
 * @mixin Media
 */
final class MediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Media $media */
        $media = $this->resource;
        $urls = app(UrlGeneratorInterface::class);

        return [
            'id' => $media->id,
            'type' => $media->type()->value,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'original_file_name' => $media->original_file_name,
            'mime_type' => $media->mime_type,
            'extension' => $media->extension,
            'size' => $media->size,
            'width' => $media->width,
            'height' => $media->height,
            'duration' => $media->duration,
            'checksum' => $media->checksum,
            'visibility' => $media->visibility->value,
            'alt_text' => $media->alt_text,
            'caption' => $media->caption,
            'copyright' => $media->copyright,
            'focal_point' => $media->focal_point,
            'dominant_color' => $media->dominant_color,
            'url' => $urls->url($media),
            'conversions' => $this->whenLoaded('conversions', fn (): array => $media->conversions
                ->map(fn ($conversion): array => [
                    'name' => $conversion->conversion_name,
                    'format' => $conversion->format,
                    'mime_type' => $conversion->mime_type,
                    'width' => $conversion->width,
                    'height' => $conversion->height,
                    'size' => $conversion->size,
                    'url' => $urls->conversionUrl($conversion),
                ])->all()),
            'created_at' => $media->created_at?->toIso8601String(),
        ];
    }
}
