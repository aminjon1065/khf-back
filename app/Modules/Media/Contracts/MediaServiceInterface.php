<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\MediaData;
use App\Modules\Media\DTOs\UpdateMetadataData;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use DateTimeInterface;

/**
 * The public API of the Media Engine. Every other module interacts with media
 * exclusively through this contract — never with Spatie or Laravel Storage.
 */
interface MediaServiceInterface
{
    public function upload(UploadMediaData $data): Media;

    public function updateMetadata(Media $media, UpdateMetadataData $data): Media;

    public function delete(Media $media, bool $permanently = false): void;

    public function restore(Media $media): Media;

    public function generateConversion(Media $media, ImageTransformation $transformation): MediaConversion;

    public function url(Media $media): string;

    public function temporaryUrl(Media $media, DateTimeInterface $expiresAt): string;

    public function toData(Media $media): MediaData;
}
