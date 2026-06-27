<?php

declare(strict_types=1);

namespace App\Modules\Navigation\DTOs;

/**
 * A navigation menu resolved for one locale and one viewer: the root nodes plus
 * the menu's identity (handle, type) and the locale it was resolved for.
 */
final class NavigationTree
{
    /**
     * @param  list<NavigationNode>  $items
     */
    public function __construct(
        public readonly string $handle,
        public readonly string $type,
        public readonly string $locale,
        public readonly array $items,
    ) {}

    public function isEmpty(): bool
    {
        return $this->items === [];
    }
}
