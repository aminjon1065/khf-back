<?php

declare(strict_types=1);

namespace App\Modules\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class SettingUpdated
{
    use Dispatchable;

    public function __construct(
        public readonly string $key,
        public readonly mixed $value,
        public readonly mixed $previous,
    ) {}
}
