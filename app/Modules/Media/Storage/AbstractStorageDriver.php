<?php

declare(strict_types=1);

namespace App\Modules\Media\Storage;

use App\Modules\Media\Contracts\StorageDriverInterface;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Exceptions\MediaStorageException;
use Illuminate\Support\Facades\Storage;

/**
 * Shared Laravel-filesystem implementation for storage drivers. Concrete drivers
 * supply only their name and temporary-URL strategy.
 */
abstract class AbstractStorageDriver implements StorageDriverInterface
{
    public function put(string $disk, string $path, string $contents, MediaVisibility $visibility): void
    {
        $ok = Storage::disk($disk)->put($path, $contents, [
            'visibility' => $visibility->toFilesystemVisibility(),
        ]);

        if ($ok === false) {
            throw MediaStorageException::writeFailed($disk, $path);
        }
    }

    public function putFile(string $disk, string $path, string $localAbsolutePath, MediaVisibility $visibility): void
    {
        $stream = fopen($localAbsolutePath, 'rb');

        if ($stream === false) {
            throw MediaStorageException::writeFailed($disk, $path);
        }

        try {
            $ok = Storage::disk($disk)->put($path, $stream, [
                'visibility' => $visibility->toFilesystemVisibility(),
            ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if ($ok === false) {
            throw MediaStorageException::writeFailed($disk, $path);
        }
    }

    public function get(string $disk, string $path): string
    {
        $contents = Storage::disk($disk)->get($path);

        if ($contents === null) {
            throw MediaStorageException::readFailed($disk, $path);
        }

        return $contents;
    }

    /**
     * @return resource
     */
    public function readStream(string $disk, string $path): mixed
    {
        $stream = Storage::disk($disk)->readStream($path);

        if ($stream === null) {
            throw MediaStorageException::readFailed($disk, $path);
        }

        return $stream;
    }

    public function delete(string $disk, string $path): void
    {
        Storage::disk($disk)->delete($path);
    }

    public function exists(string $disk, string $path): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function size(string $disk, string $path): int
    {
        return Storage::disk($disk)->size($path);
    }

    public function url(string $disk, string $path): string
    {
        return Storage::disk($disk)->url($path);
    }
}
