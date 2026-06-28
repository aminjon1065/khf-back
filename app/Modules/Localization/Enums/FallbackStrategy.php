<?php

declare(strict_types=1);

namespace App\Modules\Localization\Enums;

/**
 * How the resolver behaves when a value is missing in the requested locale:
 * `Chain` walks the fallback chain to the default, `Strict` returns only the
 * requested locale's value (or nothing).
 */
enum FallbackStrategy: string
{
    case Chain = 'chain';
    case Strict = 'strict';

    public static function fromConfig(): self
    {
        $value = config('khf.localization.fallback_strategy', 'chain');

        return self::tryFrom(is_string($value) ? $value : 'chain') ?? self::Chain;
    }
}
