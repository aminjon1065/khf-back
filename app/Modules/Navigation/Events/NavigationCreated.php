<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Events;

use App\Modules\Navigation\Models\Navigation;
use Illuminate\Foundation\Events\Dispatchable;

final class NavigationCreated
{
    use Dispatchable;

    public function __construct(public readonly Navigation $navigation) {}
}
