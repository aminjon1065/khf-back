<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Contracts\SettingTypeInterface;

/**
 * Base for setting types. By default a value is stored and read unchanged with
 * no extra rules; concrete types override only what they need. null always
 * passes through (nullability is expressed via a setting's own rules).
 */
abstract class AbstractSettingType implements SettingTypeInterface
{
    public function serialize(mixed $value): mixed
    {
        return $value;
    }

    public function cast(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @return list<string>
     */
    public function rules(): array
    {
        return [];
    }
}
