<?php

declare(strict_types=1);

namespace App\Modules\Localization\Enums;

/**
 * Writing direction of a locale's script: left-to-right or right-to-left.
 * Drives the `dir` attribute and layout mirroring on the frontends.
 */
enum TextDirection: string
{
    case Ltr = 'ltr';
    case Rtl = 'rtl';

    public function isRightToLeft(): bool
    {
        return $this === self::Rtl;
    }

    public function label(): string
    {
        return match ($this) {
            self::Ltr => 'Left to right',
            self::Rtl => 'Right to left',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $direction): string => $direction->value, self::cases());
    }
}
