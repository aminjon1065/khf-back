<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Registries;

use App\Modules\Navigation\Contracts\NavigationTypeRegistryInterface;
use App\Modules\Navigation\DTOs\NavigationTypeDefinition;

final class NavigationTypeRegistry implements NavigationTypeRegistryInterface
{
    /** @var array<string, NavigationTypeDefinition> */
    private array $types = [];

    public function register(NavigationTypeDefinition $type): void
    {
        $this->types[$type->name] = $type;
    }

    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function get(string $name): ?NavigationTypeDefinition
    {
        return $this->types[$name] ?? null;
    }

    /**
     * @return array<string, NavigationTypeDefinition>
     */
    public function all(): array
    {
        return $this->types;
    }
}
