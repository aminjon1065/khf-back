<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

/**
 * Schema-only definition for entry-to-entry references. The target collection is
 * configured through settings; the engine validates and stores only the value.
 */
final class RelationFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Relation;
    }

    public function defaultSettings(): array
    {
        return [
            'collection' => null,
            'multiple' => false,
        ];
    }
}
