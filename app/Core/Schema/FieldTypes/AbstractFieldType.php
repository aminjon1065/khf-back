<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Contracts\Schema\FieldTypeInterface;

/**
 * Base for all field type definitions. A field type describes the *schema* of a
 * field — its identity, translation default, default settings and validation —
 * never its rendering. New field types extend this class and are registered with
 * the FieldTypeRegistry, making the catalogue fully pluggable.
 */
abstract class AbstractFieldType implements FieldTypeInterface
{
    public function label(): string
    {
        return $this->type()->label();
    }

    public function isTranslatableByDefault(): bool
    {
        return $this->type()->isTranslatableByDefault();
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultSettings(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    public function defaultValidationRules(): array
    {
        return [];
    }
}
