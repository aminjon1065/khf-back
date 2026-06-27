<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Repositories;

use App\Modules\Navigation\Contracts\NavigationRepositoryInterface;
use App\Modules\Navigation\Models\Navigation;
use Illuminate\Database\Eloquent\Collection;

final class EloquentNavigationRepository implements NavigationRepositoryInterface
{
    public function findByHandle(string $handle): ?Navigation
    {
        return Navigation::query()->where('handle', $handle)->first();
    }

    /**
     * @return Collection<int, Navigation>
     */
    public function allActiveWithItems(): Collection
    {
        return Navigation::query()
            ->active()
            ->with('activeItems')
            ->get();
    }
}
