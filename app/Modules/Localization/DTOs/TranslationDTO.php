<?php

declare(strict_types=1);

namespace App\Modules\Localization\DTOs;

use App\Modules\Localization\Models\Translation;

/**
 * An immutable, transport-friendly view of a single translation row, decoupled
 * from the Eloquent model for use in events and the public API.
 */
final class TranslationDTO
{
    public function __construct(
        public readonly string $group,
        public readonly string $key,
        public readonly string $locale,
        public readonly ?string $value,
    ) {}

    public static function fromModel(Translation $t): self
    {
        return new self(
            group: $t->group,
            key: $t->key,
            locale: $t->locale,
            value: $t->value,
        );
    }

    public static function make(string $group, string $key, string $locale, ?string $value): self
    {
        return new self(
            group: $group,
            key: $key,
            locale: $locale,
            value: $value,
        );
    }

    /**
     * The "{group}.{key}" identifier used to look the value up in the cache.
     */
    public function fullKey(): string
    {
        return "{$this->group}.{$this->key}";
    }
}
