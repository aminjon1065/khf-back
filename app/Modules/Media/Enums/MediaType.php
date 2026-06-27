<?php

declare(strict_types=1);

namespace App\Modules\Media\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Audio = 'audio';
    case Document = 'document';
    case Other = 'other';

    /**
     * Classify a media type from a MIME type.
     */
    public static function fromMimeType(string $mimeType): self
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => self::Image,
            str_starts_with($mimeType, 'video/') => self::Video,
            str_starts_with($mimeType, 'audio/') => self::Audio,
            self::isDocumentMime($mimeType) => self::Document,
            default => self::Other,
        };
    }

    public function isImage(): bool
    {
        return $this === self::Image;
    }

    private static function isDocumentMime(string $mimeType): bool
    {
        return in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ], true);
    }
}
