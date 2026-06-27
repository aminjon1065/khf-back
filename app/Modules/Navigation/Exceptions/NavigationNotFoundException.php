<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Exceptions;

final class NavigationNotFoundException extends NavigationException
{
    public static function handle(string $handle): self
    {
        return new self("No navigation is registered with the handle [{$handle}].");
    }
}
