<?php

declare(strict_types=1);

use App\Modules\Navigation\Contracts\NavigationCacheInterface;
use App\Modules\Navigation\Contracts\NavigationGeneratorRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Contracts\NavigationRepositoryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationTreeBuilderInterface;
use App\Modules\Navigation\Contracts\NavigationTypeRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationVisibilityEvaluatorInterface;
use App\Modules\Navigation\DTOs\NavigationData;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Facades\Navigation as NavigationFacade;
use App\Modules\Navigation\Http\Resources\NavigationResource;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Services\NavigationSourceCache;
use Illuminate\Support\Facades\Schema;

it('binds every engine contract as a resolvable singleton', function (string $abstract) {
    expect(app($abstract))->toBeObject()
        ->and(app($abstract))->toBe(app($abstract));
})->with([
    NavigationManagerInterface::class,
    NavigationCacheInterface::class,
    NavigationTreeBuilderInterface::class,
    NavigationVisibilityEvaluatorInterface::class,
    NavigationSourceRegistryInterface::class,
    NavigationGeneratorRegistryInterface::class,
    NavigationTypeRegistryInterface::class,
    NavigationSourceCache::class,
]);

it('resolves a fresh repository per request', function () {
    expect(app(NavigationRepositoryInterface::class))
        ->not->toBe(app(NavigationRepositoryInterface::class));
});

it('exposes the manager through the Navigation facade', function () {
    NavigationFacade::createNavigation(NavigationData::make('via-facade', 'Via Facade', NavigationType::Footer));

    expect(NavigationFacade::has('via-facade'))->toBeTrue();
});

it('seeds every native navigation type', function () {
    $types = app(NavigationTypeRegistryInterface::class);

    foreach (NavigationType::values() as $value) {
        expect($types->has($value))->toBeTrue();
    }
});

it('seeds the native sources and generators', function () {
    expect(app(NavigationSourceRegistryInterface::class)->has(NavigationSourceType::Entry->value))->toBeTrue()
        ->and(app(NavigationSourceRegistryInterface::class)->has(NavigationSourceType::ExternalUrl->value))->toBeTrue()
        ->and(app(NavigationGeneratorRegistryInterface::class)->has('published_entries'))->toBeTrue();
});

it('creates the navigation tables with the expected columns', function () {
    expect(Schema::hasColumns('navigations', ['id', 'handle', 'name', 'type', 'is_active', 'settings', 'deleted_at']))->toBeTrue()
        ->and(Schema::hasColumns('navigation_items', [
            'id', 'navigation_id', 'parent_id', 'order', 'label',
            'source_type', 'source_id', 'source_value', 'target',
            'visibility', 'visibility_rules', 'generator', 'meta', 'is_active', 'deleted_at',
        ]))->toBeTrue();
});

it('serializes a resolved tree through NavigationResource', function () {
    $navigation = Navigation::factory()->create(['handle' => 'res', 'type' => NavigationType::Header]);
    NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'Home', 'ru' => 'Home', 'en' => 'Home'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/home',
    ]);

    $tree = app(NavigationManagerInterface::class)->tree('res', 'en');
    $array = (new NavigationResource($tree))->toArray(request());

    expect($array)->toMatchArray(['handle' => 'res', 'type' => 'header', 'locale' => 'en'])
        ->and($array['items'])->toHaveCount(1);
});

it('serializes nested children recursively through the resource', function () {
    $navigation = Navigation::factory()->create(['handle' => 'rec']);
    $parent = NavigationItem::factory()->forNavigation($navigation)->create([
        'label' => ['tg' => 'P', 'ru' => 'P', 'en' => 'P'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/p',
    ]);
    NavigationItem::factory()->childOf($parent)->create([
        'label' => ['tg' => 'C', 'ru' => 'C', 'en' => 'C'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/c',
    ]);

    $tree = app(NavigationManagerInterface::class)->tree('rec', 'en');
    $resource = (new NavigationResource($tree))->toArray(request());
    /** @var array<string, mixed> $json */
    $json = json_decode((string) json_encode($resource), true);

    expect($json['items'][0]['label'])->toBe('P')
        ->and($json['items'][0]['children'])->toHaveCount(1)
        ->and($json['items'][0]['children'][0]['label'])->toBe('C')
        ->and($json['items'][0]['children'][0]['url'])->toBe('/en/c')
        ->and($json['items'][0]['children'][0]['type'])->toBe('static_url')
        ->and($json['items'][0]['children'][0]['children'])->toBe([]);
});
