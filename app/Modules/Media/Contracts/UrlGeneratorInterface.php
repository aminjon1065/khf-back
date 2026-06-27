<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use DateTimeInterface;

interface UrlGeneratorInterface
{
    public function url(Media $media): string;

    public function temporaryUrl(Media $media, DateTimeInterface $expiresAt): string;

    public function conversionUrl(MediaConversion $conversion): string;
}
