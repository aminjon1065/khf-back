<?php

declare(strict_types=1);

namespace App\Modules\Localization\Enums;

/**
 * What `resolve()` returns when nothing in the fallback chain yields a value:
 * `ReturnNull` → null, `ReturnKey` → the "group.key" string, `ReturnEmpty` → ''.
 */
enum MissingTranslation: string
{
    case ReturnNull = 'null';
    case ReturnKey = 'key';
    case ReturnEmpty = 'empty';

    public static function fromConfig(): self
    {
        $value = config('khf.localization.missing_translation', 'null');

        return self::tryFrom(is_string($value) ? $value : 'null') ?? self::ReturnNull;
    }
}
