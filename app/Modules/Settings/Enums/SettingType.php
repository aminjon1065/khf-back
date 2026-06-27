<?php

declare(strict_types=1);

namespace App\Modules\Settings\Enums;

/**
 * The native, first-party setting types. Additional types are pluggable: any
 * string a SettingTypeInterface registers under becomes a valid type, so this
 * enum is a convenience catalogue of the built-ins, not a closed set.
 */
enum SettingType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case Json = 'json';
    case Arr = 'array';
    case Date = 'date';
    case DateTime = 'datetime';
    case Color = 'color';
    case Email = 'email';
    case Url = 'url';
    case Media = 'media';
    case Enum = 'enum';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $type): string => $type->value, self::cases());
    }
}
