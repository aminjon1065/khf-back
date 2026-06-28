<?php

declare(strict_types=1);

namespace App\Modules\Localization\Events;

use App\Modules\Localization\DTOs\TranslationDTO;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Dispatched after an existing translation value changes, carrying the previous value.
 */
final class TranslationUpdated
{
    use Dispatchable;

    public function __construct(
        public readonly TranslationDTO $translation,
        public readonly ?string $previous,
    ) {}
}
