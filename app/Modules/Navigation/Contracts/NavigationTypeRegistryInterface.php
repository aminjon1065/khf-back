<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Modules\Navigation\DTOs\NavigationTypeDefinition;

/**
 * Registry of navigation types (locations). Native types are seeded from the
 * NavigationType enum; plugins register further ones.
 */
interface NavigationTypeRegistryInterface
{
    public function register(NavigationTypeDefinition $type): void;

    public function has(string $name): bool;

    public function get(string $name): ?NavigationTypeDefinition;

    /**
     * @return array<string, NavigationTypeDefinition>
     */
    public function all(): array;
}
