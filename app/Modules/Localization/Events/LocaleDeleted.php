<?php

declare(strict_types=1);

namespace App\Modules\Localization\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched after a locale is deleted, carrying the removed locale code.
 */
final class LocaleDeleted
{
    use Dispatchable;

    public function __construct(public readonly string $code) {}
}
