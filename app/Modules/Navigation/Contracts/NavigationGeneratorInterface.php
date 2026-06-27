<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Modules\Navigation\DTOs\GeneratedNavigationItem;
use App\Modules\Navigation\Models\NavigationItem;

/**
 * Produces the dynamic children of a navigation item (e.g. latest news, a
 * category listing, all published pages). Runs at build time so the result is
 * cached; reads the item's `meta` for configuration.
 */
interface NavigationGeneratorInterface
{
    public function name(): string;

    /**
     * @return list<GeneratedNavigationItem>
     */
    public function generate(NavigationItem $item): array;
}
