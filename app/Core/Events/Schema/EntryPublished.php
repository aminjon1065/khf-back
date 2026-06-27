<?php

declare(strict_types=1);

namespace App\Core\Events\Schema;

use App\Core\Models\Entry;
use Illuminate\Foundation\Events\Dispatchable;

final class EntryPublished
{
    use Dispatchable;

    public function __construct(public readonly Entry $entry) {}
}
