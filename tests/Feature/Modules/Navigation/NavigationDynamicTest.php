<?php

declare(strict_types=1);

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;

beforeEach(function () {
    $this->nav = app(NavigationManagerInterface::class);
});

it('expands a dynamic item into generated children from published entries', function () {
    $collection = Collection::factory()->create(['slug' => 'news']);
    Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'alpha',
        'data' => ['en' => ['title' => 'Alpha']],
    ]);
    Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'beta',
        'data' => ['en' => ['title' => 'Beta']],
    ]);

    $navigation = Navigation::factory()->create(['handle' => 'dyn']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'News', 'ru' => 'News', 'en' => 'News'],
        'source_type' => null,
        'source_value' => null,
        'generator' => 'published_entries',
        'meta' => ['collection' => 'news', 'limit' => 10],
    ]);

    $tree = $this->nav->tree('dyn', 'en');

    expect($tree->items)->toHaveCount(1)
        ->and($tree->items[0]->children)->toHaveCount(2);

    $labels = array_map(static fn ($node): string => $node->label, $tree->items[0]->children);
    $urls = array_map(static fn ($node): ?string => $node->url, $tree->items[0]->children);

    expect($labels)->toContain('Alpha', 'Beta')
        ->and($urls)->toContain('/en/news/alpha', '/en/news/beta');
});

it('ignores draft entries in dynamic generation', function () {
    $collection = Collection::factory()->create(['slug' => 'news']);
    Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'live',
        'data' => ['en' => ['title' => 'Live']],
    ]);
    Entry::factory()->draft()->create([
        'collection_id' => $collection->id,
        'slug' => 'hidden',
        'data' => ['en' => ['title' => 'Hidden']],
    ]);

    $navigation = Navigation::factory()->create(['handle' => 'dyn2']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'News', 'ru' => 'News', 'en' => 'News'],
        'source_type' => null,
        'generator' => 'published_entries',
        'meta' => ['collection' => 'news'],
    ]);

    $children = $this->nav->tree('dyn2', 'en')->items[0]->children;

    expect($children)->toHaveCount(1)
        ->and($children[0]->label)->toBe('Live');
});

it('returns an empty subtree when the generator config references no collection', function () {
    $navigation = Navigation::factory()->create(['handle' => 'dyn3']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'News', 'ru' => 'News', 'en' => 'News'],
        'source_type' => null,
        'generator' => 'published_entries',
        'meta' => [],
    ]);

    // No children generated and no URL of its own -> the container is dropped.
    expect($this->nav->tree('dyn3', 'en')->items)->toHaveCount(0);
});
