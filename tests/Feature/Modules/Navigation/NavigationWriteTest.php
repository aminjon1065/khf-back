<?php

declare(strict_types=1);

use App\Core\Enums\EntryStatus;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\DTOs\NavigationData;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Exceptions\NavigationException;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;

beforeEach(function () {
    $this->nav = app(NavigationManagerInterface::class);
});

it('cascades a soft delete to the whole descendant subtree', function () {
    $navigation = Navigation::factory()->create();
    $parent = NavigationItem::factory()->forNavigation($navigation)->create();
    $child = NavigationItem::factory()->childOf($parent)->create();
    $grandchild = NavigationItem::factory()->childOf($child)->create();

    $this->nav->removeItem($parent);

    expect(NavigationItem::query()->find($parent->id))->toBeNull()
        ->and(NavigationItem::query()->find($child->id))->toBeNull()
        ->and(NavigationItem::query()->find($grandchild->id))->toBeNull()
        // Soft-deleted, not destroyed — recoverable.
        ->and(NavigationItem::withTrashed()->find($grandchild->id))->not->toBeNull();
});

it('rejects moving an item under itself', function () {
    $navigation = Navigation::factory()->create();
    $item = NavigationItem::factory()->forNavigation($navigation)->create();

    $this->nav->moveItem($item, $item->id, 0);
})->throws(NavigationException::class);

it('rejects moving an item under one of its own descendants', function () {
    $navigation = Navigation::factory()->create();
    $parent = NavigationItem::factory()->forNavigation($navigation)->create();
    $child = NavigationItem::factory()->childOf($parent)->create();

    $this->nav->moveItem($parent, $child->id, 0);
})->throws(NavigationException::class);

it('moves an item under a valid new parent', function () {
    $navigation = Navigation::factory()->create();
    $a = NavigationItem::factory()->forNavigation($navigation)->create();
    $b = NavigationItem::factory()->forNavigation($navigation)->create();

    $moved = $this->nav->moveItem($b, $a->id, 3);

    expect($moved->parent_id)->toBe($a->id)
        ->and($moved->order)->toBe(3);
});

it('lists navigations ordered by name', function () {
    Navigation::factory()->create(['name' => 'Zeta']);
    Navigation::factory()->create(['name' => 'Alpha']);

    $names = array_map(static fn (Navigation $n): string => $n->name, $this->nav->navigations());

    expect($names)->toBe(['Alpha', 'Zeta']);
});

it('flushes the cache when an item is removed', function () {
    $navigation = Navigation::factory()->create(['handle' => 'ri']);
    $item = NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'x', 'ru' => 'x', 'en' => 'x'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/x',
    ]);
    expect($this->nav->tree('ri', 'en')->items)->toHaveCount(1);

    $this->nav->removeItem($item);

    expect($this->nav->tree('ri', 'en')->items)->toHaveCount(0);
});

it('invalidates the cached tree when a navigation is deactivated', function () {
    $navigation = Navigation::factory()->create(['handle' => 'u']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'x', 'ru' => 'x', 'en' => 'x'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/x',
    ]);
    expect($this->nav->tree('u', 'en')->items)->toHaveCount(1);

    $this->nav->updateNavigation($navigation, NavigationData::make('u', 'U', NavigationType::Header, isActive: false));

    expect($this->nav->tree('u', 'en')->items)->toHaveCount(0);
});

it('invalidates the cache when a referenced entry is unpublished', function () {
    $collection = Collection::factory()->create(['slug' => 'news']);
    $entry = Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'hello',
        'data' => ['en' => ['title' => 'Hello']],
    ]);
    $navigation = Navigation::factory()->create(['handle' => 'ec']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'E', 'ru' => 'E', 'en' => 'E'],
        'source_type' => NavigationSourceType::Entry,
        'source_id' => $entry->id,
    ]);
    expect($this->nav->tree('ec', 'en')->items)->toHaveCount(1);

    $entry->update(['status' => EntryStatus::Draft, 'published_at' => null]);

    expect($this->nav->tree('ec', 'en')->items)->toHaveCount(0);
});
