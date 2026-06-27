<?php

declare(strict_types=1);

namespace App\Modules\Settings\Exceptions;

final class SettingNotRegisteredException extends SettingsException
{
    public static function key(string $key): self
    {
        return new self("No setting is registered for key [{$key}].");
    }
}
