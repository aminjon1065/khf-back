<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Exceptions;

final class UnknownNavigationSourceException extends NavigationException
{
    public static function type(string $type): self
    {
        return new self("No navigation source resolver is registered for type [{$type}].");
    }
}
