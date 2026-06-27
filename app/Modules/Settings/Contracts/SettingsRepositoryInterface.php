<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

interface SettingsRepositoryInterface
{
    /**
     * All persisted (overridden) values, keyed by full key.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    public function has(string $key): bool;

    /**
     * Persist a value. Returns true if the key was newly created.
     */
    public function put(string $key, ?string $group, ?string $type, mixed $value): bool;

    public function forget(string $key): bool;
}
