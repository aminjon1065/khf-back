<?php

declare(strict_types=1);

namespace App\Modules\Settings\Contracts;

use App\Modules\Settings\Exceptions\UnknownSettingTypeException;

interface SettingTypeRegistryInterface
{
    public function register(SettingTypeInterface $type): void;

    public function has(string $name): bool;

    /**
     * @throws UnknownSettingTypeException
     */
    public function get(string $name): SettingTypeInterface;

    /**
     * @return array<string, SettingTypeInterface>
     */
    public function all(): array;
}
