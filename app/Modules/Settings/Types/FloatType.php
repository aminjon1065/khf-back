<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

final class FloatType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Float->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : (float) $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (float) $value;
    }

    public function rules(): array
    {
        return ['numeric'];
    }
}
