<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;

/**
 * A datetime, stored as a normalised Y-m-d H:i:s string and read back as a
 * CarbonImmutable.
 */
final class DateTimeType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::DateTime->value;
    }

    public function serialize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof DateTimeInterface
            ? $value->format('Y-m-d H:i:s')
            : Date::parse((string) $value)->format('Y-m-d H:i:s');
    }

    public function cast(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof DateTimeInterface ? Date::parse($value) : Date::parse((string) $value);
    }

    public function rules(): array
    {
        return ['date'];
    }
}
