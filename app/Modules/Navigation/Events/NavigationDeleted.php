<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class NavigationDeleted
{
    use Dispatchable;

    public function __construct(
        public readonly string $navigationId,
        public readonly string $handle,
    ) {}
}
