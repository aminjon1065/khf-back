<?php

declare(strict_types=1);

namespace App\Modules\Localization\Events;

use App\Modules\Localization\Models\Locale;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched after an existing locale is updated.
 */
final class LocaleUpdated
{
    use Dispatchable;

    public function __construct(public readonly Locale $locale) {}
}
