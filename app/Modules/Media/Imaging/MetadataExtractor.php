<?php

declare(strict_types=1);

namespace App\Modules\Media\Imaging;

use App\Modules\Media\Contracts\MetadataExtractorInterface;
use App\Modules\Media\DTOs\ExtractedMetadata;

/**
 * Extracts intrinsic metadata using native PHP (getimagesize, exif). Video/audio
 * duration extraction is intentionally left as an extension point (would require
 * ffprobe); this implementation handles images.
 */
final class MetadataExtractor implements MetadataExtractorInterface
{
    public function extract(string $absolutePath, string $mimeType): ExtractedMetadata
    {
        if (! str_starts_with($mimeType, 'image/')) {
            return ExtractedMetadata::empty();
        }

        $dimensions = @getimagesize($absolutePath);
        $width = is_array($dimensions) ? $dimensions[0] : null;
        $height = is_array($dimensions) ? $dimensions[1] : null;

        return new ExtractedMetadata(
            width: $width,
            height: $height,
            duration: null,
            dominantColor: $this->dominantColor($absolutePath),
            exif: $this->extractExif($absolutePath, $mimeType),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function extractExif(string $absolutePath, string $mimeType): array
    {
        if (! in_array($mimeType, ['image/jpeg', 'image/tiff'], true) || ! function_exists('exif_read_data')) {
            return [];
        }

        $raw = @exif_read_data($absolutePath, null, true);

        if (! is_array($raw)) {
            return [];
        }

        return $this->sanitizeExif($raw);
    }

    private const MAX_EXIF_TAGS = 100;

    private const MAX_EXIF_VALUE_LENGTH = 512;

    /**
     * Keep only JSON-safe scalar values, drop heavy/binary/privacy-sensitive
     * sections, and cap tag count + value length to bound stored size.
     *
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function sanitizeExif(array $raw): array
    {
        $dropped = ['MAKERNOTE', 'THUMBNAIL', 'COMPUTED', 'GPS'];
        $clean = [];
        $count = 0;

        foreach ($raw as $section => $values) {
            if (in_array(strtoupper((string) $section), $dropped, true) || ! is_array($values)) {
                continue;
            }

            foreach ($values as $key => $value) {
                if ($count >= self::MAX_EXIF_TAGS) {
                    return $clean;
                }

                if (is_string($value)) {
                    $value = mb_substr($value, 0, self::MAX_EXIF_VALUE_LENGTH);
                }

                if (is_scalar($value)) {
                    $clean[$section][$key] = $value;
                    $count++;
                }
            }
        }

        return $clean;
    }

    private function dominantColor(string $absolutePath): ?string
    {
        $contents = @file_get_contents($absolutePath);

        if ($contents === false) {
            return null;
        }

        $image = @imagecreatefromstring($contents);

        if ($image === false) {
            return null;
        }

        try {
            $sample = imagecreatetruecolor(1, 1);
            imagecopyresampled($sample, $image, 0, 0, 0, 0, 1, 1, imagesx($image), imagesy($image));
            $rgb = imagecolorat($sample, 0, 0);

            return sprintf('#%02x%02x%02x', ($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF);
        } finally {
            imagedestroy($image);
            if (isset($sample)) {
                imagedestroy($sample);
            }
        }
    }
}
