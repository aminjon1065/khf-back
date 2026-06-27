<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Exceptions;

final class UnknownNavigationGeneratorException extends NavigationException
{
    public static function named(string $name): self
    {
        return new self("No navigation generator is registered with the name [{$name}].");
    }
}
