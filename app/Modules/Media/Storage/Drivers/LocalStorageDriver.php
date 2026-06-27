<?php

declare(strict_types=1);

namespace App\Modules\Media\Storage\Drivers;

use App\Modules\Media\Exceptions\MediaStorageException;
use App\Modules\Media\Storage\AbstractStorageDriver;
use DateTimeInterface;

/**
 * Local filesystem driver. Local disks cannot mint native temporary URLs, so the
 * UrlGenerator delivers private/temporary access through a signed download route
 * instead (see supportsTemporaryUrl()).
 */
final class LocalStorageDriver extends AbstractStorageDriver
{
    public function name(): string
    {
        return 'local';
    }

    public function supportsTemporaryUrl(): bool
    {
        return false;
    }

    public function temporaryUrl(string $disk, string $path, DateTimeInterface $expiresAt): string
    {
        throw new MediaStorageException('The local driver delivers temporary URLs via the signed download route, not natively.');
    }
}
