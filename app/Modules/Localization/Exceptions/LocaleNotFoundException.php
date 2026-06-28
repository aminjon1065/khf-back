<?php

declare(strict_types=1);

namespace App\Modules\Localization\Exceptions;

/**
 * Thrown when a locale is requested by code but is not registered.
 */
final class LocaleNotFoundException extends LocalizationException
{
    public static function code(string $code): self
    {
        return new self("Locale [{$code}] is not registered.");
    }
}
