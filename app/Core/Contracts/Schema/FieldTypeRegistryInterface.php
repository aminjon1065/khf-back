<?php

declare(strict_types=1);

namespace App\Core\Contracts\Schema;

use App\Core\Enums\FieldType;
use App\Core\Exceptions\CoreException;

interface FieldTypeRegistryInterface
{
    public function register(FieldTypeInterface $fieldType): void;

    /**
     * @throws CoreException when the field type has not been registered
     */
    public function get(FieldType $type): FieldTypeInterface;

    public function has(FieldType $type): bool;

    /**
     * @return array<string, FieldTypeInterface>
     */
    public function all(): array;
}
