<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class NumberFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Number;
    }

    public function defaultValidationRules(): array
    {
        return ['numeric'];
    }
}
