<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

final class IntegerType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Integer->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : (int) $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (int) $value;
    }

    public function rules(): array
    {
        return ['integer'];
    }
}
