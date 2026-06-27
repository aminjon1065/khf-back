<?php

use App\Core\Enums\EntryStatus;
use App\Core\Enums\FieldType;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use App\Core\Models\Collection;
use App\Core\Models\Entry;

it('builds the full collection → blueprint → field hierarchy', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();
    $field = BlueprintField::factory()->for($blueprint)->create();

    expect($blueprint->collection->is($collection))->toBeTrue()
        ->and($collection->blueprints->pluck('id'))->toContain($blueprint->id)
        ->and($blueprint->fields->pluck('id'))->toContain($field->id)
        ->and($field->blueprint->is($blueprint))->toBeTrue();
});

it('relates entries to their collection and blueprint', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();
    $entry = Entry::factory()->for($collection)->for($blueprint)->create();

    expect($entry->collection->is($collection))->toBeTrue()
        ->and($entry->blueprint->is($blueprint))->toBeTrue()
        ->and($collection->entries->pluck('id'))->toContain($entry->id);
});

it('orders blueprint fields by their order column', function () {
    $blueprint = Blueprint::factory()->create();
    BlueprintField::factory()->for($blueprint)->create(['handle' => 'second', 'order' => 2]);
    BlueprintField::factory()->for($blueprint)->create(['handle' => 'first', 'order' => 1]);

    expect($blueprint->fields()->pluck('handle')->all())->toBe(['first', 'second']);
});

it('casts entry status to the EntryStatus enum and data to an array', function () {
    $entry = Entry::factory()->published()->create();

    expect($entry->status)->toBeInstanceOf(EntryStatus::class)
        ->and($entry->status)->toBe(EntryStatus::Published)
        ->and($entry->data)->toBeArray()
        ->and($entry->version)->toBe(1);
});

it('casts the blueprint field type to the FieldType enum', function () {
    $field = BlueprintField::factory()->ofType(FieldType::Boolean)->create();

    expect($field->type)->toBe(FieldType::Boolean);
});

it('soft deletes entries', function () {
    $entry = Entry::factory()->create();

    $entry->delete();

    $this->assertSoftDeleted($entry);
});

it('uses a 36-character uuid primary key for entries', function () {
    $entry = Entry::factory()->create();

    expect($entry->getKeyName())->toBe('id')
        ->and($entry->id)->toBeString()
        ->and(strlen($entry->id))->toBe(36);
});

it('scopes to published entries with a past publish date only', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    Entry::factory()->for($collection)->for($blueprint)->published()->create();
    Entry::factory()->for($collection)->for($blueprint)->draft()->create();
    Entry::factory()->for($collection)->for($blueprint)->create([
        'status' => EntryStatus::Published,
        'published_at' => now()->addDay(),
    ]);

    expect(Entry::published()->count())->toBe(1);
});

it('scopes entries by locale presence in the data column', function () {
    Entry::factory()->create(['data' => ['tg' => ['title' => 'Салом']]]);

    expect(Entry::hasLocale('tg')->count())->toBe(1)
        ->and(Entry::hasLocale('ru')->count())->toBe(0);
});

it('produces persistable models from every factory', function () {
    expect(Collection::factory()->create()->exists)->toBeTrue()
        ->and(Blueprint::factory()->create()->exists)->toBeTrue()
        ->and(BlueprintField::factory()->create()->exists)->toBeTrue()
        ->and(Entry::factory()->create()->exists)->toBeTrue();
});

it('supports entry status factory states', function () {
    expect(Entry::factory()->draft()->create()->status)->toBe(EntryStatus::Draft)
        ->and(Entry::factory()->published()->create()->status)->toBe(EntryStatus::Published)
        ->and(Entry::factory()->archived()->create()->status)->toBe(EntryStatus::Archived);
});
