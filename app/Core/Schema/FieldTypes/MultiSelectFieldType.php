<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class MultiSelectFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::MultiSelect;
    }

    public function defaultValidationRules(): array
    {
        return ['array'];
    }

    public function defaultSettings(): array
    {
        return ['options' => []];
    }
}
