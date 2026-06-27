<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;

/**
 * A date, stored as a normalised Y-m-d string and read back as a CarbonImmutable.
 */
final class DateType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Date->value;
    }

    public function serialize(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof DateTimeInterface
            ? $value->format('Y-m-d')
            : Date::parse((string) $value)->format('Y-m-d');
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
