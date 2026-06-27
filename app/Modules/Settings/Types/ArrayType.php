<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

final class ArrayType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Arr->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : (array) $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (array) $value;
    }

    public function rules(): array
    {
        return ['array'];
    }
}
