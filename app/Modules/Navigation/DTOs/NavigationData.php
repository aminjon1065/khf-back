<?php

declare(strict_types=1);

namespace App\Modules\Navigation\DTOs;

use App\Modules\Navigation\Enums\NavigationType;

/**
 * Input for creating or updating a Navigation container.
 */
final class NavigationData
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly bool $isActive = true,
        public readonly array $settings = [],
    ) {}

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function make(
        string $handle,
        string $name,
        NavigationType|string $type,
        ?string $description = null,
        bool $isActive = true,
        array $settings = [],
    ): self {
        return new self(
            handle: $handle,
            name: $name,
            type: $type instanceof NavigationType ? $type->value : $type,
            description: $description,
            isActive: $isActive,
            settings: $settings,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'handle' => $this->handle,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'is_active' => $this->isActive,
            'settings' => $this->settings === [] ? null : $this->settings,
        ];
    }
}
