<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

final class BooleanType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Boolean->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (bool) $value;
    }

    public function rules(): array
    {
        return ['boolean'];
    }
}
