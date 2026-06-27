<?php

use Illuminate\Support\Facades\Schema;

it('keeps content metadata in relational columns on the entries table', function () {
    expect(Schema::hasColumns('entries', [
        'id',
        'collection_id',
        'blueprint_id',
        'author_id',
        'updated_by',
        'status',
        'slug',
        'data',
        'version',
        'published_at',
        'deleted_at',
    ]))->toBeTrue();
});

it('creates the composite publishing index on entries', function () {
    $indexes = collect(Schema::getIndexes('entries'))->pluck('name');

    expect($indexes)->toContain('entries_collection_status_published_index');
});

it('keeps the schema tables relational', function () {
    expect(Schema::hasTable('collections'))->toBeTrue()
        ->and(Schema::hasTable('blueprints'))->toBeTrue()
        ->and(Schema::hasTable('fields'))->toBeTrue()
        ->and(Schema::hasTable('entries'))->toBeTrue()
        ->and(Schema::hasColumns('fields', ['blueprint_id', 'handle', 'type', 'is_translatable', 'validation_rules', 'settings', 'order']))->toBeTrue();
});
