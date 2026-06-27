<?php

declare(strict_types=1);

namespace App\Modules\Media\Storage;

use App\Modules\Media\Contracts\StorageDriverInterface;
use App\Modules\Media\Contracts\StorageManagerInterface;
use App\Modules\Media\Exceptions\MediaStorageException;

final class StorageManager implements StorageManagerInterface
{
    /** @var array<string, StorageDriverInterface> */
    private array $drivers = [];

    /**
     * @param  iterable<StorageDriverInterface>  $drivers
     */
    public function __construct(iterable $drivers, private readonly string $default)
    {
        foreach ($drivers as $driver) {
            $this->register($driver);
        }
    }

    public function driver(?string $name = null): StorageDriverInterface
    {
        $name ??= $this->default;

        if (! isset($this->drivers[$name])) {
            throw MediaStorageException::unknownDriver($name);
        }

        return $this->drivers[$name];
    }

    public function register(StorageDriverInterface $driver): void
    {
        $this->drivers[$driver->name()] = $driver;
    }

    public function defaultDriver(): string
    {
        return $this->default;
    }
}
