<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Enums\MediaVisibility;
use DateTimeInterface;

/**
 * A pluggable storage backend. Each driver wraps a Laravel filesystem disk and
 * adds backend-specific behaviour (notably temporary/signed URL generation).
 * New backends are added by implementing this interface and registering the
 * driver with the StorageManager.
 */
interface StorageDriverInterface
{
    public function name(): string;

    public function put(string $disk, string $path, string $contents, MediaVisibility $visibility): void;

    public function putFile(string $disk, string $path, string $localAbsolutePath, MediaVisibility $visibility): void;

    public function get(string $disk, string $path): string;

    /**
     * @return resource
     */
    public function readStream(string $disk, string $path): mixed;

    public function delete(string $disk, string $path): void;

    public function exists(string $disk, string $path): bool;

    public function size(string $disk, string $path): int;

    public function url(string $disk, string $path): string;

    public function temporaryUrl(string $disk, string $path, DateTimeInterface $expiresAt): string;

    public function supportsTemporaryUrl(): bool;
}
