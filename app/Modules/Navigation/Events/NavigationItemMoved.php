<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Events;

use App\Modules\Navigation\Models\NavigationItem;
use Illuminate\Foundation\Events\Dispatchable;

final class NavigationItemMoved
{
    use Dispatchable;

    public function __construct(
        public readonly NavigationItem $item,
        public readonly ?string $previousParentId,
        public readonly int $previousOrder,
    ) {}
}
