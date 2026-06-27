<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

/**
 * Schema-only definition for media references. The Media module (future sprint)
 * will consume this field type; the engine stores only the reference value.
 */
final class MediaFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Media;
    }

    public function defaultSettings(): array
    {
        return ['multiple' => false];
    }
}
