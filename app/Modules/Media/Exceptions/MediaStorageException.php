<?php

declare(strict_types=1);

namespace App\Modules\Media\Exceptions;

final class MediaStorageException extends MediaException
{
    public static function writeFailed(string $disk, string $path): self
    {
        return new self("Failed to write to disk [{$disk}] at path [{$path}].");
    }

    public static function readFailed(string $disk, string $path): self
    {
        return new self("Failed to read from disk [{$disk}] at path [{$path}].");
    }

    public static function unknownDriver(string $driver): self
    {
        return new self("No storage driver is registered for [{$driver}].");
    }
}
