<?php

declare(strict_types=1);

namespace App\Core\Schema;

use App\Core\Contracts\Schema\FieldTypeInterface;
use App\Core\Contracts\Schema\FieldTypeRegistryInterface;
use App\Core\Enums\FieldType;
use App\Core\Exceptions\CoreException;

/**
 * Pluggable catalogue of field types, keyed by FieldType backing value.
 *
 * Bound as a singleton by CoreServiceProvider and seeded with the twelve
 * built-in types. Future modules register additional types by resolving this
 * registry and calling register().
 */
final class FieldTypeRegistry implements FieldTypeRegistryInterface
{
    /** @var array<string, FieldTypeInterface> */
    private array $types = [];

    public function register(FieldTypeInterface $fieldType): void
    {
        $this->types[$fieldType->type()->value] = $fieldType;
    }

    public function get(FieldType $type): FieldTypeInterface
    {
        if (! $this->has($type)) {
            throw new CoreException("Field type [{$type->value}] is not registered.");
        }

        return $this->types[$type->value];
    }

    public function has(FieldType $type): bool
    {
        return isset($this->types[$type->value]);
    }

    /**
     * @return array<string, FieldTypeInterface>
     */
    public function all(): array
    {
        return $this->types;
    }
}
