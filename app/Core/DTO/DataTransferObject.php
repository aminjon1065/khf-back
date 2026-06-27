<?php

declare(strict_types=1);

namespace App\Core\DTO;

use ReflectionClass;
use ReflectionProperty;

/**
 * Base class for all CMS data transfer objects.
 *
 * Concrete DTOs use PHP 8.1+ readonly constructor promotion:
 *
 *   final class CreateEntryDto extends DataTransferObject {
 *       public function __construct(
 *           public readonly string $slug,
 *           public readonly string $status,
 *       ) {}
 *   }
 *
 * The base class provides toArray() via reflection over public properties,
 * which covers both readonly and regular public properties uniformly.
 */
abstract class DataTransferObject
{
    /**
     * Return all public properties as an associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflect = new ReflectionClass($this);
        $result = [];

        foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }
}
