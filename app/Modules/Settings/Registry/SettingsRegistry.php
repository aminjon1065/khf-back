<?php

declare(strict_types=1);

namespace App\Modules\Settings\Registry;

use App\Modules\Settings\Contracts\SettingsRegistryInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\DTOs\SettingGroup;

/**
 * In-memory registry of setting definitions and groups. Populated by modules
 * during boot; bound as a singleton so registrations persist for the request.
 */
final class SettingsRegistry implements SettingsRegistryInterface
{
    /** @var array<string, SettingDefinition> */
    private array $definitions = [];

    /** @var array<string, SettingGroup> */
    private array $groups = [];

    public function register(SettingDefinition $definition): void
    {
        $this->definitions[$definition->fullKey()] = $definition;

        // Auto-register a bare group if the module did not declare one.
        if (! isset($this->groups[$definition->group])) {
            $this->groups[$definition->group] = new SettingGroup($definition->group);
        }
    }

    public function registerGroup(SettingGroup $group): void
    {
        $this->groups[$group->name] = $group;
    }

    public function definition(string $key): ?SettingDefinition
    {
        return $this->definitions[$key] ?? null;
    }

    public function hasDefinition(string $key): bool
    {
        return isset($this->definitions[$key]);
    }

    /**
     * @return array<string, SettingDefinition>
     */
    public function definitions(): array
    {
        return $this->definitions;
    }

    /**
     * @return list<SettingDefinition>
     */
    public function forGroup(string $group): array
    {
        return array_values(array_filter(
            $this->definitions,
            static fn (SettingDefinition $definition): bool => $definition->group === $group,
        ));
    }

    /**
     * @return array<string, SettingGroup>
     */
    public function groups(): array
    {
        return $this->groups;
    }
}
