<?php

declare(strict_types=1);

namespace App\Modules\Localization\Events;

use App\Modules\Localization\DTOs\TranslationDTO;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched after a new translation value is persisted.
 */
final class TranslationCreated
{
    use Dispatchable;

    public function __construct(public readonly TranslationDTO $translation) {}
}
