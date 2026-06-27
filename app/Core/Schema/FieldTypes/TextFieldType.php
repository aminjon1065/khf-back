<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class TextFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Text;
    }

    public function defaultValidationRules(): array
    {
        return ['string', 'max:255'];
    }
}
