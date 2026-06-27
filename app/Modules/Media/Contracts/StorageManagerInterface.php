<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Exceptions\MediaStorageException;

interface StorageManagerInterface
{
    /**
     * Resolve a storage driver by name, or the default driver when null.
     *
     * @throws MediaStorageException when the named driver is not registered
     */
    public function driver(?string $name = null): StorageDriverInterface;

    public function register(StorageDriverInterface $driver): void;

    public function defaultDriver(): string;
}
