<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

final class UrlType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Url->value;
    }

    public function serialize(mixed $value): mixed
    {
        return $value === null ? null : (string) $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value === null ? null : (string) $value;
    }

    public function rules(): array
    {
        return ['url'];
    }
}
