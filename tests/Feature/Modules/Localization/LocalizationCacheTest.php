<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocalizationCacheInterface;
use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\Translation;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    Locale::factory()->default()->create(['alias' => 'tj', 'sort_order' => 1]);
    Locale::factory()->create(['code' => 'ru', 'fallback_code' => 'tg', 'sort_order' => 2]);
    Locale::factory()->create(['code' => 'en', 'fallback_code' => 'tg', 'sort_order' => 3]);

    $this->cache = app(LocalizationCacheInterface::class);
    $this->service = app(LocalizationServiceInterface::class);
});

it('reads the locale registry from a single query within one request', function () {
    $this->cache->flush();
    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->cache->locales();
    $this->cache->locales();
    $this->cache->locales();

    expect(DB::getQueryLog())->toHaveCount(1);
});

it('reads a per-locale translation map from a single query within one request', function () {
    $this->service->setTranslation('ui', 'a', 'tg', 'one');
    $this->service->setTranslation('ui', 'b', 'tg', 'two');
    $this->cache->flush();

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->cache->translations('tg');
    $this->cache->translations('tg');

    expect(DB::getQueryLog())->toHaveCount(1);
});

it('serves a stale translation map from cache until it is flushed', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'first');
    $this->cache->flush();

    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'first']);

    // Write straight to the table, bypassing model events, then prove the cache
    // still serves the stale value until it is explicitly flushed.
    DB::table('translations')
        ->where('group', 'ui')->where('key', 'save')->where('locale', 'tg')
        ->update(['value' => 'second']);

    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'first']);

    $this->cache->flush();

    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'second']);
});

it('serves a stale locale registry from cache until it is flushed', function () {
    $this->cache->flush();
    expect($this->cache->locales()->firstWhere('code', 'ru')?->name)->not->toBeNull();

    // Direct DB write bypasses Locale::saved, so the cache stays stale.
    DB::table('locales')->where('code', 'ru')->update(['name' => 'Mutated']);

    expect($this->cache->locales()->firstWhere('code', 'ru')?->name)->not->toBe('Mutated');

    $this->cache->flush();

    expect($this->cache->locales()->firstWhere('code', 'ru')?->name)->toBe('Mutated');
});

it('flushTranslations invalidates only the requested locale map', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'tg-first');
    $this->service->setTranslation('ui', 'save', 'ru', 'ru-first');
    $this->cache->flush();

    // Prime both locale maps into the cache + memo.
    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'tg-first']);
    expect($this->cache->translations('ru'))->toMatchArray(['ui.save' => 'ru-first']);

    DB::table('translations')->where('locale', 'tg')->update(['value' => 'tg-second']);
    DB::table('translations')->where('locale', 'ru')->update(['value' => 'ru-second']);

    $this->cache->flushTranslations('tg');

    // tg was invalidated → fresh; ru was untouched → still stale.
    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'tg-second'])
        ->and($this->cache->translations('ru'))->toMatchArray(['ui.save' => 'ru-first']);
});

it('warm() primes the locale registry and active translation maps', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'Захира');
    $this->service->setTranslation('ui', 'save', 'ru', 'Сохранить');

    $this->cache->warm();

    DB::flushQueryLog();
    DB::enableQueryLog();

    // Already warmed → reads are served from the in-request memo, no queries.
    expect($this->cache->locales()->pluck('code')->all())->toContain('tg', 'ru', 'en');
    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'Захира']);
    expect($this->cache->translations('ru'))->toMatchArray(['ui.save' => 'Сохранить']);

    expect(DB::getQueryLog())->toHaveCount(0);
});

it('a service write flushes the cache so subsequent reads are fresh', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'first');
    expect($this->service->translations('tg'))->toMatchArray(['ui.save' => 'first']);

    $this->service->setTranslation('ui', 'save', 'tg', 'second');

    expect($this->service->translations('tg'))->toMatchArray(['ui.save' => 'second']);
});

it('keeps the cache coherent when a translation is saved directly through the model', function () {
    // Model events (Translation::saved) flush the cache, unlike raw DB writes.
    $this->service->setTranslation('ui', 'save', 'tg', 'first');
    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'first']);

    Translation::query()
        ->where('group', 'ui')->where('key', 'save')->where('locale', 'tg')
        ->first()?->update(['value' => 'third']);

    expect($this->cache->translations('tg'))->toMatchArray(['ui.save' => 'third']);
});
