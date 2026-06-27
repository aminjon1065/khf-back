<?php

declare(strict_types=1);

use App\Modules\Settings\Contracts\SettingsCacheInterface;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Services\SettingsCache;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;

beforeEach(function () {
    $this->settings = app(SettingsManagerInterface::class);
});

it('reads every persisted value from a single query within one request', function () {
    Setting::factory()->create(['key' => 'general.a', 'group' => 'general', 'type' => 'string', 'value' => 'one']);
    Setting::factory()->create(['key' => 'general.b', 'group' => 'general', 'type' => 'string', 'value' => 'two']);

    app(SettingsCacheInterface::class)->flush();
    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->settings->get('general.a');
    $this->settings->get('general.b');
    $this->settings->get('general.a');

    expect(DB::getQueryLog())->toHaveCount(1);
});

it('serves values from cache without re-querying until flushed', function () {
    $cache = app(SettingsCacheInterface::class);
    Setting::factory()->create(['key' => 'general.x', 'group' => 'general', 'type' => 'string', 'value' => 'first']);

    $cache->warm();
    expect($cache->values())->toMatchArray(['general.x' => 'first']);

    // Write straight to the table, bypassing the model events, then prove the
    // cache still serves the stale value until it is explicitly flushed.
    DB::table('settings')->where('key', 'general.x')->update(['value' => json_encode('second')]);
    expect($cache->values())->toMatchArray(['general.x' => 'first']);

    $cache->flush();
    expect($cache->values())->toMatchArray(['general.x' => 'second']);
});

it('invalidates the cache when a write goes through the manager', function () {
    $this->settings->register(SettingDefinition::make('general', 'count', SettingType::Integer, 0));
    expect($this->settings->get('general.count'))->toBe(0);

    $this->settings->set('general.count', 7);

    expect($this->settings->get('general.count'))->toBe(7);
});

it('excludes legacy singleton (bare-key) rows from the engine value map', function () {
    // Legacy singletons live on the same table under bare keys; the engine must
    // never fold them into its own value map.
    App\Models\Setting::put('president', ['name' => 'X']);
    Setting::factory()->create(['key' => 'general.real', 'group' => 'general', 'type' => 'string', 'value' => 'r']);

    $cache = app(SettingsCacheInterface::class);
    $cache->warm();

    expect($cache->values())->toHaveKey('general.real')
        ->and($cache->values())->not->toHaveKey('president');
});

it('warm() rebuilds the map from the repository', function () {
    Setting::factory()->create(['key' => 'general.warm', 'group' => 'general', 'type' => 'string', 'value' => 'w']);

    app(SettingsCacheInterface::class)->warm();

    expect(app(SettingsCacheInterface::class)->values())->toMatchArray(['general.warm' => 'w']);
});

it('caches with a bounded TTL via remember() when ttl > 0', function () {
    $map = ['general.ttl' => 'cached'];
    $store = Mockery::mock(CacheContract::class, function (MockInterface $mock) use ($map): void {
        $mock->shouldReceive('remember')->once()
            ->with('khf.settings.ttl-test', 60, Mockery::type('Closure'))
            ->andReturn($map);
        $mock->shouldNotReceive('rememberForever');
    });

    $cache = new SettingsCache(app(SettingsRepositoryInterface::class), $store, 'khf.settings.ttl-test', 60);

    expect($cache->values())->toBe($map);
});

it('caches forever via rememberForever() when ttl = 0', function () {
    $map = ['general.forever' => 'cached'];
    $store = Mockery::mock(CacheContract::class, function (MockInterface $mock) use ($map): void {
        $mock->shouldReceive('rememberForever')->once()
            ->with('khf.settings.forever-test', Mockery::type('Closure'))
            ->andReturn($map);
        $mock->shouldNotReceive('remember');
    });

    $cache = new SettingsCache(app(SettingsRepositoryInterface::class), $store, 'khf.settings.forever-test', 0);

    expect($cache->values())->toBe($map);
});
