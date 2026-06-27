<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\DTOs\SettingGroup;

/**
 * The public API of the Settings Engine. Every module reads and writes
 * configurable values exclusively through this contract (or the Settings
 * facade) — no module creates its own settings storage.
 */
interface SettingsManagerInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;

    public function set(string $key, mixed $value): void;

    public function forget(string $key): void;

    /**
     * All resolved settings (defaults overlaid with persisted values), by key.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * Resolved settings for a single group, by key.
     *
     * @return array<string, mixed>
     */
    public function forGroup(string $group): array;

    // --- Registration (modules call these during boot) ---

    public function register(SettingDefinition $definition): void;

    public function registerGroup(SettingGroup $group): void;

    public function registerType(SettingTypeInterface $type): void;

    // --- Cache ---

    public function warm(): void;

    public function flush(): void;

    // --- Import / Export ---

    public function export(string $format = 'json'): string;

    /**
     * @return list<string> the keys that were imported
     */
    public function import(string $payload, string $format = 'json'): array;
}
