<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Sources;

use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Services\NavigationSourceCache;

/**
 * Resolves an item pointing at a CMS Entry to /{locale}/{collection.slug}/{entry.slug}.
 * Only published entries that are translated for the locale resolve — otherwise
 * the item is unavailable for that locale. The published entry is loaded once per
 * build (shared cache) and the per-locale translation gate is applied in PHP.
 */
final class EntrySourceResolver implements NavigationSourceResolverInterface
{
    public function __construct(private readonly NavigationSourceCache $cache) {}

    public function type(): string
    {
        return NavigationSourceType::Entry->value;
    }

    public function resolve(NavigationItem $item, string $locale): ?string
    {
        if ($item->source_id === null) {
            return null;
        }

        $entry = $this->cache->remember(
            'entry:'.$item->source_id,
            fn (): ?Entry => Entry::query()->with('collection')->published()->find($item->source_id),
        );

        if (! $entry instanceof Entry || $entry->collection === null || $entry->slug === null) {
            return null;
        }

        $data = $entry->data ?? [];
        if (! isset($data[$locale])) {
            return null; // not translated for this locale
        }

        return '/'.implode('/', [$locale, $entry->collection->slug, $entry->slug]);
    }
}
