<?php

declare(strict_types=1);

namespace App\Modules\Localization\Exceptions;

/**
 * Thrown when creating a locale whose code is already registered.
 */
final class DuplicateLocaleException extends LocalizationException
{
    public static function code(string $code): self
    {
        return new self("Locale [{$code}] already exists.");
    }
}
