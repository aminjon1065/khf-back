<?php

declare(strict_types=1);

namespace App\Modules\Identity\Contracts;

/**
 * Registry of known permission names. Seeded from the canonical catalogue and
 * extended by plugins (via the REGISTER_PERMISSIONS hook) so new permissions
 * can be added without modifying the engine.
 */
interface PermissionRegistryInterface
{
    public function register(string $permission): void;

    public function has(string $permission): bool;

    /**
     * @return list<string>
     */
    public function all(): array;
}
