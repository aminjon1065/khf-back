<?php

declare(strict_types=1);

namespace App\Modules\Navigation\DTOs;

use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationVisibility;

/**
 * Input for creating a NavigationItem.
 */
final class NavigationItemData
{
    /**
     * @param  array<string, string>  $label  locale => title
     * @param  list<string>  $visibilityRules
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public readonly string $navigationId,
        public readonly array $label,
        public readonly ?string $parentId = null,
        public readonly int $order = 0,
        public readonly ?string $sourceType = null,
        public readonly ?string $sourceId = null,
        public readonly ?string $sourceValue = null,
        public readonly string $target = '_self',
        public readonly string $visibility = NavigationVisibility::Public->value,
        public readonly array $visibilityRules = [],
        public readonly ?string $generator = null,
        public readonly array $meta = [],
        public readonly bool $isActive = true,
    ) {}

    /**
     * @param  array<string, string>  $label
     * @param  list<string>  $visibilityRules
     * @param  array<string, mixed>  $meta
     */
    public static function make(
        string $navigationId,
        array $label,
        ?string $parentId = null,
        int $order = 0,
        NavigationSourceType|string|null $sourceType = null,
        ?string $sourceId = null,
        ?string $sourceValue = null,
        string $target = '_self',
        NavigationVisibility|string $visibility = NavigationVisibility::Public,
        array $visibilityRules = [],
        ?string $generator = null,
        array $meta = [],
        bool $isActive = true,
    ): self {
        return new self(
            navigationId: $navigationId,
            label: $label,
            parentId: $parentId,
            order: $order,
            sourceType: $sourceType instanceof NavigationSourceType ? $sourceType->value : $sourceType,
            sourceId: $sourceId,
            sourceValue: $sourceValue,
            target: $target,
            visibility: $visibility instanceof NavigationVisibility ? $visibility->value : $visibility,
            visibilityRules: $visibilityRules,
            generator: $generator,
            meta: $meta,
            isActive: $isActive,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'navigation_id' => $this->navigationId,
            'parent_id' => $this->parentId,
            'order' => $this->order,
            'label' => $this->label,
            'source_type' => $this->sourceType,
            'source_id' => $this->sourceId,
            'source_value' => $this->sourceValue,
            'target' => $this->target,
            'visibility' => $this->visibility,
            'visibility_rules' => $this->visibilityRules === [] ? null : $this->visibilityRules,
            'generator' => $this->generator,
            'meta' => $this->meta === [] ? null : $this->meta,
            'is_active' => $this->isActive,
        ];
    }
}
