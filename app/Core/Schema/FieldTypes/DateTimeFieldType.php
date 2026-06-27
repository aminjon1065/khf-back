<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class DateTimeFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::DateTime;
    }

    public function defaultValidationRules(): array
    {
        return ['date'];
    }

    public function defaultSettings(): array
    {
        return ['format' => 'Y-m-d H:i:s'];
    }
}
