<?php

declare(strict_types=1);

namespace App\Modules\Navigation\DTOs;

/**
 * A single resolved node of a navigation tree: label and URL already resolved
 * for one locale, with its visible children. This is the output shape consumed
 * by Resources / the frontend.
 */
final class NavigationNode
{
    /**
     * @param  array<string, mixed>  $meta
     * @param  list<NavigationNode>  $children
     */
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly ?string $url,
        public readonly string $target,
        public readonly ?string $sourceType,
        public readonly bool $active,
        public readonly array $meta = [],
        public readonly array $children = [],
    ) {}

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }
}
