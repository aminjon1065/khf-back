<?php

declare(strict_types=1);

namespace App\Modules\Media\Factories;

use App\Modules\Media\Models\Media;
use App\Modules\Media\Pipeline\UploadContext;

/**
 * Builds a (yet-unsaved) Media model from a completed upload context. Keeps
 * model assembly in one place, decoupled from the persistence stage.
 */
final class MediaFactory
{
    public function fromContext(UploadContext $context): Media
    {
        $media = new Media;
        $media->id = $context->mediaId;
        $media->driver = $context->driver;
        $media->disk = $context->disk;
        $media->path = (string) $context->storedPath;
        $media->visibility = $context->visibility;
        $media->name = $context->name;
        $media->file_name = $context->fileName;
        $media->original_file_name = $context->originalFileName;
        $media->mime_type = $context->mimeType;
        $media->extension = $context->extension !== '' ? $context->extension : null;
        $media->size = $context->size;
        $media->width = $context->metadata->width;
        $media->height = $context->metadata->height;
        $media->duration = $context->metadata->duration;
        $media->checksum = $context->checksum;
        $media->alt_text = $context->altText;
        $media->caption = $context->caption;
        $media->copyright = $context->copyright;
        $media->dominant_color = $context->metadata->dominantColor;
        $media->exif = $context->metadata->exif !== [] ? $context->metadata->exif : null;
        $media->custom_properties = $context->customProperties !== [] ? $context->customProperties : null;
        $media->uploaded_by = $context->uploadedBy;

        return $media;
    }
}
