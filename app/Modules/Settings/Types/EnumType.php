<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

/**
 * A value constrained to a fixed set of options, stored as a string. The allowed
 * options are supplied per-setting via its own rules (e.g. ['in:light,dark']).
 */
final class EnumType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Enum->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : (string) $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (string) $value;
    }
}
