<?php

declare(strict_types=1);

namespace App\Modules\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class SettingDeleted
{
    use Dispatchable;

    public function __construct(public readonly string $key) {}
}
