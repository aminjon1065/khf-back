<?php

declare(strict_types=1);

namespace App\Modules\Media\Url;

use App\Modules\Media\Contracts\StorageManagerInterface;
use App\Modules\Media\Contracts\UrlGeneratorInterface;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

final class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private readonly StorageManagerInterface $storage,
        private readonly int $temporaryLifetimeMinutes,
    ) {}

    public function url(Media $media): string
    {
        // Private assets are never exposed at a stable public URL; serve them
        // through a short-lived signed URL instead.
        if ($media->visibility === MediaVisibility::Private) {
            return $this->temporaryUrl($media, Carbon::now()->addMinutes($this->temporaryLifetimeMinutes));
        }

        return $this->storage->driver($media->driver)->url($media->disk, $media->path);
    }

    public function temporaryUrl(Media $media, DateTimeInterface $expiresAt): string
    {
        $driver = $this->storage->driver($media->driver);

        if ($driver->supportsTemporaryUrl()) {
            return $driver->temporaryUrl($media->disk, $media->path, $expiresAt);
        }

        return URL::temporarySignedRoute('media.download', $expiresAt, ['media' => $media->id]);
    }

    public function conversionUrl(MediaConversion $conversion): string
    {
        // A derivative of a private original must not be exposed at a stable
        // public URL — gate it behind a signed download route too.
        if ($conversion->visibility === MediaVisibility::Private) {
            return URL::temporarySignedRoute('media.conversion.download', Carbon::now()->addMinutes($this->temporaryLifetimeMinutes), ['conversion' => $conversion->id]);
        }

        return $this->storage->driver($conversion->driver)->url($conversion->disk, $conversion->path);
    }
}
