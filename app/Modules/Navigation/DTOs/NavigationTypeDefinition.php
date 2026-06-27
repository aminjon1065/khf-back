<?php

declare(strict_types=1);

namespace App\Modules\Navigation\DTOs;

use App\Modules\Navigation\Enums\NavigationType;

/**
 * A registered navigation type (location). Native types are seeded from
 * NavigationType; plugins register further ones through the registry.
 */
final class NavigationTypeDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly string $label,
    ) {}

    public static function fromEnum(NavigationType $type): self
    {
        return new self($type->value, $type->label());
    }
}
