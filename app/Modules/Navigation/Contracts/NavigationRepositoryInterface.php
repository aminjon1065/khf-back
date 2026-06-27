<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Modules\Navigation\Models\Navigation;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read access to navigation aggregates for the tree builder and cache.
 */
interface NavigationRepositoryInterface
{
    public function findByHandle(string $handle): ?Navigation;

    /**
     * Every active navigation with its (active) items eager-loaded, ready for the
     * tree builder to assemble — the single query behind the cache.
     *
     * @return Collection<int, Navigation>
     */
    public function allActiveWithItems(): Collection;
}
