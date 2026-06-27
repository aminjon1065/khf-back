<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class BooleanFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Boolean;
    }

    public function defaultValidationRules(): array
    {
        return ['boolean'];
    }
}
