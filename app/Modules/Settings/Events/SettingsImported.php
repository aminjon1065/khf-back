<?php

declare(strict_types=1);

namespace App\Modules\Settings\Events;

use Illuminate\Foundation\Events\Dispatchable;

final class SettingsImported
{
    use Dispatchable;

    /**
     * @param  list<string>  $keys
     */
    public function __construct(
        public readonly array $keys,
        public readonly string $format,
    ) {}
}
