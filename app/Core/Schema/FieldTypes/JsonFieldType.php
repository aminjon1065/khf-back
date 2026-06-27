<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class JsonFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Json;
    }

    public function defaultValidationRules(): array
    {
        return ['array'];
    }
}
