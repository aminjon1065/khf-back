<?php

declare(strict_types=1);

namespace App\Modules\Settings\Exceptions;

final class UnknownSettingTypeException extends SettingsException
{
    public static function named(string $type): self
    {
        return new self("No setting type is registered for [{$type}].");
    }
}
