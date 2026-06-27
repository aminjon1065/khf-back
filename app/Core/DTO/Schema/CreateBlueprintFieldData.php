<?php

declare(strict_types=1);

namespace App\Core\DTO\Schema;

use App\Core\Contracts\Schema\FieldTypeInterface;
use App\Core\DTO\DataTransferObject;
use App\Core\Enums\FieldType;

final class CreateBlueprintFieldData extends DataTransferObject
{
    /**
     * @param  list<string>|null  $validationRules
     * @param  array<array-key, mixed>|null  $settings
     */
    public function __construct(
        public readonly string $blueprintId,
        public readonly string $name,
        public readonly string $handle,
        public readonly FieldType $type,
        public readonly ?bool $isTranslatable = null,
        public readonly ?array $validationRules = null,
        public readonly ?array $settings = null,
        public readonly int $order = 0,
    ) {}

    /**
     * Return a copy with all optional members resolved from the field type's
     * defaults. Keeps default resolution out of the persistence layer while
     * preserving DTO immutability.
     */
    public function withResolvedDefaults(FieldTypeInterface $fieldType): self
    {
        return new self(
            blueprintId: $this->blueprintId,
            name: $this->name,
            handle: $this->handle,
            type: $this->type,
            isTranslatable: $this->isTranslatable ?? $fieldType->isTranslatableByDefault(),
            validationRules: $this->validationRules ?? $fieldType->defaultValidationRules(),
            settings: $this->settings ?? $fieldType->defaultSettings(),
            order: $this->order,
        );
    }
}
