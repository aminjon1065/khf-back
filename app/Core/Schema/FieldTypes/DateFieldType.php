<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class DateFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Date;
    }

    public function defaultValidationRules(): array
    {
        return ['date'];
    }

    public function defaultSettings(): array
    {
        return ['format' => 'Y-m-d'];
    }
}
