<?php

declare(strict_types=1);

namespace App\Modules\Settings\Facades;

use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\DTOs\SettingGroup;
use App\Modules\Settings\Services\SettingsManager;
use Illuminate\Support\Facades\Facade;

/**
 * Static entry point to the Settings Engine.
 *
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static void set(string $key, mixed $value)
 * @method static void forget(string $key)
 * @method static array<string, mixed> all()
 * @method static array<string, mixed> forGroup(string $group)
 * @method static void register(SettingDefinition $definition)
 * @method static void registerGroup(SettingGroup $group)
 * @method static void registerType(\App\Modules\Settings\Contracts\SettingTypeInterface $type)
 * @method static void warm()
 * @method static void flush()
 * @method static string export(string $format = 'json')
 * @method static list<string> import(string $payload, string $format = 'json')
 *
 * @see SettingsManager
 */
final class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsManagerInterface::class;
    }
}
