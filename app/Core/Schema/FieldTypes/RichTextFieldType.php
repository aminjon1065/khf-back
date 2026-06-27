<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class RichTextFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::RichText;
    }

    public function defaultValidationRules(): array
    {
        return ['string'];
    }
}
