<?php

use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a complete schema hierarchy and save an entry', function () {
    $collection = Collection::create([
        'name' => 'News',
        'slug' => 'news',
        'description' => 'Latest updates',
    ]);

    $blueprint = Blueprint::create([
        'collection_id' => $collection->id,
        'name' => 'Default News Blueprint',
    ]);

    BlueprintField::create([
        'blueprint_id' => $blueprint->id,
        'name' => 'Title',
        'handle' => 'title',
        'type' => 'text',
        'is_translatable' => true,
    ]);

    $entry = Entry::create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'status' => 'published',
        'slug' => 'hello-world',
        'published_at' => now(),
        'data' => [
            'tg' => [
                'title' => 'Салом',
            ],
            'en' => [
                'title' => 'Hello',
            ],
        ],
    ]);

    expect($entry->id)->not->toBeNull()
        ->and($entry->collection->name)->toBe('News')
        ->and($entry->data['tg']['title'])->toBe('Салом');

    // Test the local filtering scope
    $hasTg = Entry::hasLocale('tg')->count();
    $hasRu = Entry::hasLocale('ru')->count();

    expect($hasTg)->toBe(1)
        ->and($hasRu)->toBe(0);
});
