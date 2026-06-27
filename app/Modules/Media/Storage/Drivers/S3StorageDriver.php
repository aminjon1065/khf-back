<?php

declare(strict_types=1);

namespace App\Modules\Media\Storage\Drivers;

use App\Modules\Media\Storage\AbstractStorageDriver;
use DateTimeInterface;
use Illuminate\Support\Facades\Storage;

/**
 * S3-compatible driver. Delegates to the underlying flysystem S3 adapter for
 * native pre-signed temporary URLs.
 */
final class S3StorageDriver extends AbstractStorageDriver
{
    public function name(): string
    {
        return 's3';
    }

    public function supportsTemporaryUrl(): bool
    {
        return true;
    }

    public function temporaryUrl(string $disk, string $path, DateTimeInterface $expiresAt): string
    {
        return Storage::disk($disk)->temporaryUrl($path, $expiresAt);
    }
}
