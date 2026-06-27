<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

/**
 * Caches the fully-built, multi-locale navigation trees so reads avoid the
 * database. Lazily warmed on first access; invalidated on every write.
 */
interface NavigationCacheInterface
{
    /**
     * The built tree for every navigation, keyed by handle.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public function all(): array;

    /**
     * The built root nodes for one navigation, or null when it does not exist.
     *
     * @return list<array<string, mixed>>|null
     */
    public function get(string $handle): ?array;

    public function warm(): void;

    public function flush(): void;
}
