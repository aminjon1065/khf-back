<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Generators;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationGeneratorInterface;
use App\Modules\Navigation\DTOs\GeneratedNavigationItem;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Support\NavigationLocales;

/**
 * Generates child items from the published entries of a collection — covering
 * "Latest News", "All Published Pages" and "Category Listing" via the item's
 * `meta` config. Custom queries are served by plugins registering their own
 * generators.
 *
 * Supported `meta` keys: collection (slug, required), limit (int, default 10),
 * order ('latest'|'oldest', default 'latest'), title_field (default 'title').
 */
final class PublishedEntriesGenerator implements NavigationGeneratorInterface
{
    public function name(): string
    {
        return 'published_entries';
    }

    /**
     * @return list<GeneratedNavigationItem>
     */
    public function generate(NavigationItem $item): array
    {
        $meta = $item->meta ?? [];

        $collectionSlug = is_string($meta['collection'] ?? null) ? $meta['collection'] : null;
        if ($collectionSlug === null) {
            return [];
        }

        $collection = Collection::query()->where('slug', $collectionSlug)->first();
        if ($collection === null) {
            return [];
        }

        $limit = is_int($meta['limit'] ?? null) ? max(1, $meta['limit']) : 10;
        $titleField = is_string($meta['title_field'] ?? null) ? $meta['title_field'] : 'title';
        $oldestFirst = ($meta['order'] ?? 'latest') === 'oldest';

        $query = Entry::query()
            ->where('collection_id', $collection->id)
            ->published()
            ->limit($limit);

        $oldestFirst ? $query->orderBy('published_at') : $query->orderByDesc('published_at');

        $items = [];

        foreach ($query->get() as $entry) {
            if ($entry->slug === null) {
                continue;
            }

            $generated = $this->toGeneratedItem($entry, $collection->slug, $titleField);
            if ($generated !== null) {
                $items[] = $generated;
            }
        }

        return $items;
    }

    private function toGeneratedItem(Entry $entry, string $collectionSlug, string $titleField): ?GeneratedNavigationItem
    {
        $data = $entry->data ?? [];
        $labels = [];
        $urls = [];

        foreach (NavigationLocales::all() as $locale) {
            $localeData = $data[$locale] ?? null;
            if (! is_array($localeData)) {
                continue; // not translated for this locale
            }

            $title = is_string($localeData[$titleField] ?? null) ? $localeData[$titleField] : (string) $entry->slug;
            $labels[$locale] = $title;
            $urls[$locale] = '/'.$locale.'/'.$collectionSlug.'/'.$entry->slug;
        }

        if ($labels === []) {
            return null; // not translated in any locale
        }

        return new GeneratedNavigationItem($labels, $urls, '_self', ['entry_id' => $entry->id]);
    }
}
