<?php

declare(strict_types=1);

namespace App\Modules\Settings\Registry;

use App\Modules\Settings\Contracts\SettingTypeInterface;
use App\Modules\Settings\Contracts\SettingTypeRegistryInterface;
use App\Modules\Settings\Exceptions\UnknownSettingTypeException;

/**
 * Pluggable catalogue of setting types, keyed by name. Seeded with the native
 * types and extended by plugins via the REGISTER hook.
 */
final class SettingTypeRegistry implements SettingTypeRegistryInterface
{
    /** @var array<string, SettingTypeInterface> */
    private array $types = [];

    public function register(SettingTypeInterface $type): void
    {
        $this->types[$type->name()] = $type;
    }

    public function has(string $name): bool
    {
        return isset($this->types[$name]);
    }

    public function get(string $name): SettingTypeInterface
    {
        if (! $this->has($name)) {
            throw UnknownSettingTypeException::named($name);
        }

        return $this->types[$name];
    }

    /**
     * @return array<string, SettingTypeInterface>
     */
    public function all(): array
    {
        return $this->types;
    }
}
