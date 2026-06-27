<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

/**
 * A hex colour string (#rgb or #rrggbb), normalised to lower-case.
 */
final class ColorType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Color->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : strtolower((string) $value);
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (string) $value;
    }

    public function rules(): array
    {
        return ['string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'];
    }
}
