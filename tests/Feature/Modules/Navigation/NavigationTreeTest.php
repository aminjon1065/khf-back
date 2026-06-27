<?php

declare(strict_types=1);

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Exceptions\NavigationNotFoundException;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;

beforeEach(function () {
    $this->nav = app(NavigationManagerInterface::class);
});

it('builds the root items ordered by their order column', function () {
    $navigation = Navigation::factory()->create(['handle' => 'main', 'type' => NavigationType::Header]);
    NavigationItem::factory()->forNavigation($navigation)->ordered(2)->create([
        'label' => ['tg' => 'B', 'ru' => 'B', 'en' => 'B'],
        'source_type' => NavigationSourceType::ExternalUrl,
        'source_value' => 'https://b.test',
    ]);
    NavigationItem::factory()->forNavigation($navigation)->ordered(1)->create([
        'label' => ['tg' => 'A', 'ru' => 'A', 'en' => 'A'],
        'source_type' => NavigationSourceType::ExternalUrl,
        'source_value' => 'https://a.test',
    ]);

    $tree = $this->nav->tree('main', 'en');

    expect($tree->handle)->toBe('main')
        ->and($tree->type)->toBe('header')
        ->and($tree->items)->toHaveCount(2)
        ->and($tree->items[0]->label)->toBe('A')
        ->and($tree->items[0]->url)->toBe('https://a.test')
        ->and($tree->items[1]->label)->toBe('B');
});

it('nests children to unlimited depth', function () {
    $navigation = Navigation::factory()->create(['handle' => 'deep']);
    $root = NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'root', 'ru' => 'root', 'en' => 'root'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/root',
    ]);
    $child = NavigationItem::factory()->childOf($root)->create([
        'label' => ['tg' => 'child', 'ru' => 'child', 'en' => 'child'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/child',
    ]);
    NavigationItem::factory()->childOf($child)->create([
        'label' => ['tg' => 'gc', 'ru' => 'gc', 'en' => 'gc'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/gc',
    ]);

    $tree = $this->nav->tree('deep', 'en');

    expect($tree->items)->toHaveCount(1)
        ->and($tree->items[0]->children)->toHaveCount(1)
        ->and($tree->items[0]->children[0]->children)->toHaveCount(1)
        ->and($tree->items[0]->children[0]->children[0]->label)->toBe('gc')
        ->and($tree->items[0]->children[0]->children[0]->url)->toBe('/en/gc');
});

it('resolves the label for the requested locale', function () {
    $navigation = Navigation::factory()->create(['handle' => 'loc']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'Сар', 'ru' => 'Главная', 'en' => 'Home'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/home',
    ]);

    expect($this->nav->tree('loc', 'ru')->items[0]->label)->toBe('Главная')
        ->and($this->nav->tree('loc', 'en')->items[0]->label)->toBe('Home');
});

it('falls back to the tg label when the locale is missing', function () {
    $navigation = Navigation::factory()->create(['handle' => 'fallback']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'ТолькоТоҷикӣ'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/x',
    ]);

    expect($this->nav->tree('fallback', 'en')->items[0]->label)->toBe('ТолькоТоҷикӣ');
});

it('locale-prefixes static URLs', function () {
    $navigation = Navigation::factory()->create(['handle' => 'su']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'x', 'ru' => 'x', 'en' => 'x'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => 'about',
    ]);

    expect($this->nav->tree('su', 'ru')->items[0]->url)->toBe('/ru/about');
});

it('resolves an internal entry link to /{locale}/{collection}/{entry}', function () {
    $collection = Collection::factory()->create(['slug' => 'news']);
    $entry = Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'hello',
        'data' => ['en' => ['title' => 'Hello'], 'tg' => ['title' => 'Салом']],
    ]);
    $navigation = Navigation::factory()->create(['handle' => 'entry-nav']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'E', 'ru' => 'E', 'en' => 'E'],
        'source_type' => NavigationSourceType::Entry,
        'source_value' => null,
        'source_id' => $entry->id,
    ]);

    expect($this->nav->tree('entry-nav', 'en')->items[0]->url)->toBe('/en/news/hello');
});

it('drops an entry item for a locale it is not translated into', function () {
    $collection = Collection::factory()->create(['slug' => 'news']);
    $entry = Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'slug' => 'hello',
        'data' => ['en' => ['title' => 'Hello']],
    ]);
    $navigation = Navigation::factory()->create(['handle' => 'entry-nav']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'E', 'ru' => 'E', 'en' => 'E'],
        'source_type' => NavigationSourceType::Entry,
        'source_id' => $entry->id,
    ]);

    expect($this->nav->tree('entry-nav', 'en')->items)->toHaveCount(1)
        ->and($this->nav->tree('entry-nav', 'ru')->items)->toHaveCount(0);
});

it('throws when the navigation handle is unknown', function () {
    $this->nav->tree('does-not-exist');
})->throws(NavigationNotFoundException::class);

it('hides the entire subtree of an inactive parent', function () {
    $navigation = Navigation::factory()->create(['handle' => 'ip']);
    $parent = NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'p', 'ru' => 'p', 'en' => 'p'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/p',
        'is_active' => false,
    ]);
    NavigationItem::factory()->childOf($parent)->create([
        'label' => ['tg' => 'c', 'ru' => 'c', 'en' => 'c'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/c',
        'is_active' => true,
    ]);

    expect($this->nav->tree('ip', 'en')->items)->toHaveCount(0);
});

it('excludes inactive items from the tree', function () {
    $navigation = Navigation::factory()->create(['handle' => 'act']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'on', 'ru' => 'on', 'en' => 'on'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/on',
        'is_active' => true,
    ]);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'off', 'ru' => 'off', 'en' => 'off'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/off',
        'is_active' => false,
    ]);

    $tree = $this->nav->tree('act', 'en');

    expect($tree->items)->toHaveCount(1)
        ->and($tree->items[0]->label)->toBe('on');
});
