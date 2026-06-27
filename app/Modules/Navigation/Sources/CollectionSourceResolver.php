<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Sources;

use App\Core\Models\Collection;
use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Services\NavigationSourceCache;

/**
 * Resolves an item pointing at a Collection to its localized index page,
 * /{locale}/{collection.slug}. The collection is loaded once per build.
 */
final class CollectionSourceResolver implements NavigationSourceResolverInterface
{
    public function __construct(private readonly NavigationSourceCache $cache) {}

    public function type(): string
    {
        return NavigationSourceType::Collection->value;
    }

    public function resolve(NavigationItem $item, string $locale): ?string
    {
        if ($item->source_id === null) {
            return null;
        }

        $collection = $this->cache->remember(
            'collection:'.$item->source_id,
            fn (): ?Collection => Collection::query()->find($item->source_id),
        );

        if (! $collection instanceof Collection) {
            return null;
        }

        return '/'.$locale.'/'.$collection->slug;
    }
}
