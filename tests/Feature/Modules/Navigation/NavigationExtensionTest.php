<?php

declare(strict_types=1);

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Contracts\NavigationTypeRegistryInterface;
use App\Modules\Navigation\DTOs\NavigationData;
use App\Modules\Navigation\DTOs\NavigationNode;
use App\Modules\Navigation\DTOs\NavigationTypeDefinition;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Exceptions\NavigationException;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Support\NavigationHooks;

beforeEach(function () {
    $this->nav = app(NavigationManagerInterface::class);
});

it('lets a MODIFY_TREE listener inject an item into the resolved tree', function () {
    Navigation::factory()->create(['handle' => 'mt']);

    app(HookManagerInterface::class)->addFilter(
        NavigationHooks::MODIFY_TREE,
        function (array $nodes): array {
            $nodes[] = new NavigationNode('injected', 'Injected', '/injected', '_self', null, true);

            return $nodes;
        },
    );

    $tree = $this->nav->tree('mt', 'en');

    expect($tree->items)->toHaveCount(1)
        ->and($tree->items[0]->label)->toBe('Injected');
});

it('drops non-NavigationNode values returned from a MODIFY_TREE listener', function () {
    $navigation = Navigation::factory()->create(['handle' => 'mt2']);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'real', 'ru' => 'real', 'en' => 'real'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/r',
    ]);

    app(HookManagerInterface::class)->addFilter(
        NavigationHooks::MODIFY_TREE,
        fn (array $nodes): array => [...$nodes, 'not-a-node', 42],
    );

    expect($this->nav->tree('mt2', 'en')->items)->toHaveCount(1);
});

it('registers and uses a custom navigation type', function () {
    $this->nav->registerType(new NavigationTypeDefinition('landing', 'Landing'));
    $this->nav->createNavigation(NavigationData::make('lp', 'Landing Page', 'landing'));

    expect(app(NavigationTypeRegistryInterface::class)->has('landing'))->toBeTrue()
        ->and($this->nav->tree('lp', 'en')->type)->toBe('landing');
});

it('rejects creating a navigation with an unregistered type', function () {
    $this->nav->createNavigation(NavigationData::make('bad', 'Bad', 'no-such-type'));
})->throws(NavigationException::class);

it('serializes a tree for every native navigation type', function (NavigationType $type) {
    $navigation = Navigation::factory()->create(['handle' => 'type-'.$type->value, 'type' => $type]);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'x', 'ru' => 'x', 'en' => 'x'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/x',
    ]);

    $tree = $this->nav->tree('type-'.$type->value, 'en');

    expect($tree->type)->toBe($type->value)
        ->and($tree->items)->toHaveCount(1);
})->with(NavigationType::cases());
