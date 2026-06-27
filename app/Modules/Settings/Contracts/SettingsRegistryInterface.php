<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\DTOs\SettingGroup;

/**
 * In-memory registry of setting definitions and groups, populated by modules
 * during boot. The single source of truth for which settings exist, their
 * defaults, types and grouping.
 */
interface SettingsRegistryInterface
{
    public function register(SettingDefinition $definition): void;

    public function registerGroup(SettingGroup $group): void;

    public function definition(string $key): ?SettingDefinition;

    public function hasDefinition(string $key): bool;

    /**
     * @return array<string, SettingDefinition>
     */
    public function definitions(): array;

    /**
     * @return list<SettingDefinition>
     */
    public function forGroup(string $group): array;

    /**
     * @return array<string, SettingGroup>
     */
    public function groups(): array;
}
