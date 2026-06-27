<?php

use App\Core\Contracts\HookManagerInterface;
use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\DTO\Schema\CreateBlueprintData;
use App\Core\DTO\Schema\CreateBlueprintFieldData;
use App\Core\DTO\Schema\CreateCollectionData;
use App\Core\DTO\Schema\CreateEntryData;
use App\Core\DTO\Schema\UpdateEntryData;
use App\Core\Enums\EntryStatus;
use App\Core\Enums\FieldType;
use App\Core\Events\Schema\BlueprintCreated;
use App\Core\Events\Schema\CollectionCreated;
use App\Core\Events\Schema\EntryArchived;
use App\Core\Events\Schema\EntryCreated;
use App\Core\Events\Schema\EntryPublished;
use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Core\Schema\SchemaHooks;
use App\Models\User;
use Illuminate\Support\Facades\Event;

/**
 * The engine is resolved *after* Event::fake() so the EventBus singleton is
 * constructed against the fake dispatcher.
 */
function engine(): SchemaEngineInterface
{
    return app(SchemaEngineInterface::class);
}

it('creates a collection and dispatches CollectionCreated', function () {
    Event::fake([CollectionCreated::class]);

    $collection = engine()->createCollection(new CreateCollectionData('News', 'news', 'Latest'));

    expect($collection->exists)->toBeTrue()
        ->and($collection->slug)->toBe('news')
        ->and($collection->description)->toBe('Latest');
    Event::assertDispatched(CollectionCreated::class);
});

it('creates a blueprint under a collection and dispatches BlueprintCreated', function () {
    $collection = Collection::factory()->create();
    Event::fake([BlueprintCreated::class]);

    $blueprint = engine()->createBlueprint(new CreateBlueprintData($collection->id, 'Article'));

    expect($blueprint->collection_id)->toBe($collection->id)
        ->and($blueprint->name)->toBe('Article');
    Event::assertDispatched(BlueprintCreated::class);
});

it('adds a field and resolves defaults from its field type', function () {
    $blueprint = Blueprint::factory()->create();

    $field = engine()->addField(
        new CreateBlueprintFieldData($blueprint->id, 'Title', 'title', FieldType::Text),
    );

    expect($field->type)->toBe(FieldType::Text)
        ->and($field->is_translatable)->toBeTrue()
        ->and($field->validation_rules)->toBe(['string', 'max:255']);
});

it('honours explicit field settings over defaults', function () {
    $blueprint = Blueprint::factory()->create();

    $field = engine()->addField(new CreateBlueprintFieldData(
        blueprintId: $blueprint->id,
        name: 'Category',
        handle: 'category',
        type: FieldType::Select,
        isTranslatable: false,
        validationRules: ['required', 'string'],
        settings: ['options' => ['a', 'b']],
    ));

    expect($field->validation_rules)->toBe(['required', 'string'])
        ->and($field->settings)->toBe(['options' => ['a', 'b']]);
});

it('creates a draft entry without a publish date and dispatches EntryCreated only', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();
    Event::fake([EntryCreated::class, EntryPublished::class]);

    $entry = engine()->createEntry(new CreateEntryData(
        collectionId: $collection->id,
        blueprintId: $blueprint->id,
        status: EntryStatus::Draft,
        data: ['global' => ['title' => 'Hello']],
    ));

    expect($entry->status)->toBe(EntryStatus::Draft)
        ->and($entry->version)->toBe(1)
        ->and($entry->published_at)->toBeNull()
        ->and($entry->slug)->not->toBeNull();
    Event::assertDispatched(EntryCreated::class);
    Event::assertNotDispatched(EntryPublished::class);
});

it('creating a published entry stamps published_at and dispatches both events', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();
    Event::fake([EntryCreated::class, EntryPublished::class]);

    $entry = engine()->createEntry(new CreateEntryData(
        collectionId: $collection->id,
        blueprintId: $blueprint->id,
        status: EntryStatus::Published,
        data: ['global' => ['title' => 'Live']],
    ));

    expect($entry->published_at)->not->toBeNull();
    Event::assertDispatched(EntryCreated::class);
    Event::assertDispatched(EntryPublished::class);
});

it('generates unique slugs for entries that share a title candidate', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    $first = engine()->createEntry(new CreateEntryData(
        $collection->id, $blueprint->id, EntryStatus::Draft, ['global' => ['title' => 'Same Title']], slug: 'Same Title',
    ));
    $second = engine()->createEntry(new CreateEntryData(
        $collection->id, $blueprint->id, EntryStatus::Draft, ['global' => ['title' => 'Same Title']], slug: 'Same Title',
    ));

    expect($first->slug)->toBe('same-title')
        ->and($second->slug)->toBe('same-title-2');
});

it('publishes a draft entry, bumps the version and records the actor', function () {
    $actor = User::factory()->create();
    $entry = Entry::factory()->draft()->create();
    Event::fake([EntryPublished::class]);

    $published = engine()->publishEntry($entry, $actor->id);

    expect($published->status)->toBe(EntryStatus::Published)
        ->and($published->published_at)->not->toBeNull()
        ->and($published->version)->toBe(2)
        ->and($published->updated_by)->toBe($actor->id);
    Event::assertDispatched(EntryPublished::class);
});

it('treats publishing an already published entry as a no-op', function () {
    $entry = Entry::factory()->published()->create(['version' => 3]);
    Event::fake([EntryPublished::class]);

    $result = engine()->publishEntry($entry);

    expect($result->version)->toBe(3);
    Event::assertNotDispatched(EntryPublished::class);
});

it('archives an entry, bumps the version and dispatches EntryArchived', function () {
    $actor = User::factory()->create();
    $entry = Entry::factory()->published()->create(['version' => 1]);
    Event::fake([EntryArchived::class]);

    $archived = engine()->archiveEntry($entry, $actor->id);

    expect($archived->status)->toBe(EntryStatus::Archived)
        ->and($archived->version)->toBe(2)
        ->and($archived->updated_by)->toBe($actor->id);
    Event::assertDispatched(EntryArchived::class);
});

it('updates entry content and bumps the version', function () {
    $actor = User::factory()->create();
    $entry = Entry::factory()->draft()->create(['data' => ['global' => ['t' => 'old']], 'version' => 1]);

    $updated = engine()->updateEntry($entry, new UpdateEntryData(
        data: ['global' => ['t' => 'new']],
        updatedBy: $actor->id,
    ));

    expect($updated->data['global']['t'])->toBe('new')
        ->and($updated->version)->toBe(2)
        ->and($updated->updated_by)->toBe($actor->id);
});

it('emits EntryPublished when an update transitions the entry to published', function () {
    $entry = Entry::factory()->draft()->create();
    Event::fake([EntryPublished::class]);

    engine()->updateEntry($entry, new UpdateEntryData(
        data: ['global' => ['t' => 'x']],
        status: EntryStatus::Published,
    ));

    expect($entry->fresh()->published_at)->not->toBeNull();
    Event::assertDispatched(EntryPublished::class);
});

it('fires the entry-created hook as an integration point', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    $captured = null;
    app(HookManagerInterface::class)->addAction(
        SchemaHooks::ENTRY_CREATED,
        function (Entry $entry) use (&$captured): void {
            $captured = $entry->id;
        },
    );

    $entry = engine()->createEntry(new CreateEntryData(
        $collection->id, $blueprint->id, EntryStatus::Draft, ['global' => ['title' => 'Hook']],
    ));

    expect($captured)->toBe($entry->id);
});

it('reuses a slug freed only by soft-deletion without breaching the unique index', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    $first = engine()->createEntry(new CreateEntryData(
        $collection->id, $blueprint->id, EntryStatus::Draft, ['global' => ['title' => 'Post']], slug: 'Post',
    ));
    expect($first->slug)->toBe('post');

    $first->delete(); // soft delete keeps the row (and its unique slug) in the table

    $second = engine()->createEntry(new CreateEntryData(
        $collection->id, $blueprint->id, EntryStatus::Draft, ['global' => ['title' => 'Post']], slug: 'Post',
    ));

    expect($second->slug)->toBe('post-2');
});

it('emits EntryArchived when an update transitions the entry to archived', function () {
    $entry = Entry::factory()->published()->create();
    Event::fake([EntryArchived::class]);

    engine()->updateEntry($entry, new UpdateEntryData(
        data: ['global' => ['t' => 'x']],
        status: EntryStatus::Archived,
    ));

    expect($entry->fresh()->status)->toBe(EntryStatus::Archived);
    Event::assertDispatched(EntryArchived::class);
});
