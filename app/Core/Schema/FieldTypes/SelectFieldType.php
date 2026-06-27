<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class SelectFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Select;
    }

    public function defaultValidationRules(): array
    {
        return ['string'];
    }

    public function defaultSettings(): array
    {
        return ['options' => []];
    }
}
