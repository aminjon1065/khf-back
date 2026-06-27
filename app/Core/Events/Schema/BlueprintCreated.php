<?php

declare(strict_types=1);

namespace App\Core\Events\Schema;

use App\Core\Models\Blueprint;
use Illuminate\Foundation\Events\Dispatchable;

final class BlueprintCreated
{
    use Dispatchable;

    public function __construct(public readonly Blueprint $blueprint) {}
}
