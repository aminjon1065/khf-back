<?php

declare(strict_types=1);

namespace App\Modules\Settings\DTOs;

use App\Modules\Settings\Enums\SettingType;

/**
 * The immutable definition of a single setting: where it lives (group/key), its
 * type, its default value and its extra validation rules. Definitions live in
 * the in-memory registry; the database only stores overridden values.
 */
final class SettingDefinition
{
    /**
     * @param  string  $type  a registered setting-type name (SettingType::X->value or a plugin type)
     * @param  list<string>  $rules  extra validation rules, merged with the type's defaults
     */
    public function __construct(
        public readonly string $group,
        public readonly string $key,
        public readonly string $type,
        public readonly mixed $default = null,
        public readonly array $rules = [],
        public readonly ?string $label = null,
        public readonly ?string $description = null,
    ) {}

    /**
     * @param  list<string>  $rules
     */
    public static function make(
        string $group,
        string $key,
        SettingType|string $type,
        mixed $default = null,
        array $rules = [],
        ?string $label = null,
        ?string $description = null,
    ): self {
        return new self(
            group: $group,
            key: $key,
            type: $type instanceof SettingType ? $type->value : $type,
            default: $default,
            rules: $rules,
            label: $label,
            description: $description,
        );
    }

    /**
     * The globally-unique key under which the value is stored/looked up.
     */
    public function fullKey(): string
    {
        return "{$this->group}.{$this->key}";
    }
}
