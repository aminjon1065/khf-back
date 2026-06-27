<?php

declare(strict_types=1);

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationCacheInterface;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Contracts\NavigationTreeBuilderInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Services\NavigationCache;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;

function staticItem(Navigation $navigation, string $label): void
{
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => $label, 'ru' => $label, 'en' => $label],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/x',
    ]);
}

it('builds every tree from a constant number of queries within one request', function () {
    $navigation = Navigation::factory()->create(['handle' => 'c']);
    staticItem($navigation, 'one');

    app(NavigationCacheInterface::class)->flush();
    DB::flushQueryLog();
    DB::enableQueryLog();

    app(NavigationCacheInterface::class)->all();
    app(NavigationCacheInterface::class)->all();
    app(NavigationCacheInterface::class)->all();

    // navigations + eager activeItems, once; subsequent reads hit the memo.
    expect(DB::getQueryLog())->toHaveCount(2);
});

it('loads a shared entry source once per build, collapsing the N+1', function () {
    $collection = Collection::factory()->create(['slug' => 'news']);
    $entry = Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'shared',
        'data' => ['tg' => ['title' => 'S'], 'ru' => ['title' => 'S'], 'en' => ['title' => 'S']],
    ]);
    $navigation = Navigation::factory()->create(['handle' => 'shared']);
    foreach (range(1, 3) as $i) {
        NavigationItem::factory()->forNavigation($navigation)->ordered($i)->create([
            'label' => ['tg' => "i{$i}", 'ru' => "i{$i}", 'en' => "i{$i}"],
            'source_type' => NavigationSourceType::Entry,
            'source_id' => $entry->id,
        ]);
    }

    app(NavigationCacheInterface::class)->flush();
    DB::flushQueryLog();
    DB::enableQueryLog();
    app(NavigationCacheInterface::class)->all();

    // 3 items x 3 locales = 9 resolutions of the same entry, but the per-build
    // identity map loads the entries row only once.
    $entryQueries = collect(DB::getQueryLog())
        ->filter(fn (array $log): bool => str_contains((string) $log['query'], '"entries"'));

    expect($entryQueries)->toHaveCount(1);
});

it('serves the cached tree until it is flushed', function () {
    $navigation = Navigation::factory()->create(['handle' => 'stale']);
    $item = NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'first', 'ru' => 'first', 'en' => 'first'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/x',
    ]);

    $manager = app(NavigationManagerInterface::class);
    $manager->warm();
    expect($manager->tree('stale', 'en')->items[0]->label)->toBe('first');

    // Bypass model events with a raw update so the cache is not auto-invalidated.
    DB::table('navigation_items')->where('id', $item->id)->update([
        'label' => json_encode(['tg' => 'second', 'ru' => 'second', 'en' => 'second']),
    ]);
    expect($manager->tree('stale', 'en')->items[0]->label)->toBe('first');

    $manager->flush();
    expect($manager->tree('stale', 'en')->items[0]->label)->toBe('second');
});

it('invalidates the cache when an item is written through the model', function () {
    $navigation = Navigation::factory()->create(['handle' => 'inv']);
    $manager = app(NavigationManagerInterface::class);
    expect($manager->tree('inv', 'en')->items)->toHaveCount(0);

    staticItem($navigation, 'new');

    expect($manager->tree('inv', 'en')->items)->toHaveCount(1);
});

it('caches with a bounded TTL via remember() when ttl > 0', function () {
    $map = ['h' => []];
    $store = Mockery::mock(CacheContract::class, function (MockInterface $mock) use ($map): void {
        $mock->shouldReceive('remember')->once()
            ->with('khf.navigation.ttl-test', 60, Mockery::type('Closure'))
            ->andReturn($map);
        $mock->shouldNotReceive('rememberForever');
    });

    $cache = new NavigationCache(app(NavigationTreeBuilderInterface::class), $store, 'khf.navigation.ttl-test', 60);

    expect($cache->all())->toBe($map);
});

it('caches forever via rememberForever() when ttl = 0', function () {
    $map = ['h' => []];
    $store = Mockery::mock(CacheContract::class, function (MockInterface $mock) use ($map): void {
        $mock->shouldReceive('rememberForever')->once()
            ->with('khf.navigation.forever-test', Mockery::type('Closure'))
            ->andReturn($map);
        $mock->shouldNotReceive('remember');
    });

    $cache = new NavigationCache(app(NavigationTreeBuilderInterface::class), $store, 'khf.navigation.forever-test', 0);

    expect($cache->all())->toBe($map);
});
