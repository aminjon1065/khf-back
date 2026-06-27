<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

/**
 * Assembles every active navigation into a fully-resolved, multi-locale tree of
 * plain node arrays — URLs resolved for every locale and dynamic items expanded
 * — ready to be cached. Per-locale selection and visibility filtering happen
 * later, at read time, from this cached structure.
 */
interface NavigationTreeBuilderInterface
{
    /**
     * @return array<string, list<array<string, mixed>>> handle => root node arrays
     */
    public function buildAll(): array;
}
