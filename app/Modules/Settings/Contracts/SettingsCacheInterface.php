<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

/**
 * Caches the full map of persisted setting values so reads avoid the database.
 * Lazily warmed on first access; invalidated on every write.
 */
interface SettingsCacheInterface
{
    /**
     * The cached value map (lazily warmed from the repository).
     *
     * @return array<string, mixed>
     */
    public function values(): array;

    /**
     * Eagerly (re)build the cache from the repository.
     */
    public function warm(): void;

    /**
     * Invalidate the cache so the next read re-warms it.
     */
    public function flush(): void;
}
