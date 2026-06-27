<?php

declare(strict_types=1);

namespace App\Core\Events\Schema;

use App\Core\Models\Collection;
use Illuminate\Foundation\Events\Dispatchable;

final class CollectionCreated
{
    use Dispatchable;

    public function __construct(public readonly Collection $collection) {}
}
