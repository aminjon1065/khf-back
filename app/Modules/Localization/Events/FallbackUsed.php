<?php

declare(strict_types=1);

namespace App\Modules\Localization\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched when a value could not be resolved in the requested locale and a
 * fallback locale was used instead. The context is a "group.key" or a label.
 */
final class FallbackUsed
{
    use Dispatchable;

    public function __construct(
        public readonly string $requestedLocale,
        public readonly string $resolvedLocale,
        public readonly string $context,
    ) {}
}
