<?php

declare(strict_types=1);

namespace App\Core\Contracts\Schema;

use App\Core\Enums\FieldType;

interface FieldTypeInterface
{
    public function type(): FieldType;

    public function label(): string;

    public function isTranslatableByDefault(): bool;

    /** @return array<string, mixed> */
    public function defaultSettings(): array;

    /** @return list<string> */
    public function defaultValidationRules(): array;
}
