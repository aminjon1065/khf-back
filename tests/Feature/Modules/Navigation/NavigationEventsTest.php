<?php

declare(strict_types=1);

use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\DTOs\NavigationData;
use App\Modules\Navigation\DTOs\NavigationItemData;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Events\NavigationCreated;
use App\Modules\Navigation\Events\NavigationDeleted;
use App\Modules\Navigation\Events\NavigationItemCreated;
use App\Modules\Navigation\Events\NavigationItemMoved;
use App\Modules\Navigation\Events\NavigationPublished;
use App\Modules\Navigation\Events\NavigationUpdated;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use Illuminate\Support\Facades\Event;

it('dispatches NavigationCreated on create', function () {
    Event::fake([NavigationCreated::class]);
    $manager = app(NavigationManagerInterface::class);

    $navigation = $manager->createNavigation(NavigationData::make('main', 'Main', NavigationType::Header));

    Event::assertDispatched(NavigationCreated::class, fn (NavigationCreated $e): bool => $e->navigation->is($navigation));
});

it('dispatches NavigationUpdated on update', function () {
    Event::fake([NavigationUpdated::class]);
    $manager = app(NavigationManagerInterface::class);
    $navigation = Navigation::factory()->create();

    $manager->updateNavigation($navigation, NavigationData::make($navigation->handle, 'Renamed', NavigationType::Footer));

    Event::assertDispatched(NavigationUpdated::class, fn (NavigationUpdated $e): bool => $e->navigation->name === 'Renamed');
});

it('dispatches NavigationDeleted on delete', function () {
    Event::fake([NavigationDeleted::class]);
    $manager = app(NavigationManagerInterface::class);
    $navigation = Navigation::factory()->create(['handle' => 'gone']);

    $manager->deleteNavigation($navigation);

    Event::assertDispatched(NavigationDeleted::class, fn (NavigationDeleted $e): bool => $e->handle === 'gone');
});

it('dispatches NavigationPublished on publish', function () {
    Event::fake([NavigationPublished::class]);
    $manager = app(NavigationManagerInterface::class);
    $navigation = Navigation::factory()->inactive()->create();

    $published = $manager->publishNavigation($navigation);

    expect($published->is_active)->toBeTrue();
    Event::assertDispatched(NavigationPublished::class);
});

it('dispatches NavigationItemCreated when an item is added', function () {
    Event::fake([NavigationItemCreated::class]);
    $manager = app(NavigationManagerInterface::class);
    $navigation = Navigation::factory()->create();

    $item = $manager->addItem(NavigationItemData::make(
        navigationId: $navigation->id,
        label: ['tg' => 'X', 'ru' => 'X', 'en' => 'X'],
        sourceType: NavigationSourceType::ExternalUrl,
        sourceValue: 'https://x.test',
    ));

    Event::assertDispatched(NavigationItemCreated::class, fn (NavigationItemCreated $e): bool => $e->item->is($item));
});

it('dispatches NavigationItemMoved with the previous position', function () {
    Event::fake([NavigationItemMoved::class]);
    $manager = app(NavigationManagerInterface::class);
    $navigation = Navigation::factory()->create();
    $item = NavigationItem::factory()->forNavigation($navigation)->ordered(1)->create();

    $manager->moveItem($item, null, 5);

    Event::assertDispatched(
        NavigationItemMoved::class,
        fn (NavigationItemMoved $e): bool => $e->previousOrder === 1 && $e->item->order === 5 && $e->previousParentId === null,
    );
});
