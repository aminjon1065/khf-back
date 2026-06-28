<?php

declare(strict_types=1);

namespace App\Modules\Localization\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched after a translation row is removed.
 */
final class TranslationDeleted
{
    use Dispatchable;

    public function __construct(
        public readonly string $group,
        public readonly string $key,
        public readonly string $locale,
    ) {}
}
