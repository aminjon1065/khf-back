<?php

declare(strict_types=1);

namespace App\Core\Schema\FieldTypes;

use App\Core\Enums\FieldType;

final class TextareaFieldType extends AbstractFieldType
{
    public function type(): FieldType
    {
        return FieldType::Textarea;
    }

    public function defaultValidationRules(): array
    {
        return ['string'];
    }

    public function defaultSettings(): array
    {
        return ['rows' => 4];
    }
}
