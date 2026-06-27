<?php

use App\Core\Enums\EntryStatus;
use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::where('email', 'admin@khf.tj')->first();
    $this->editor = User::where('email', 'editor@khf.tj')->first();

    // Create test schema
    $this->collection = Collection::create(['name' => 'News', 'slug' => 'news']);
    $this->blueprint = Blueprint::create(['collection_id' => $this->collection->id, 'name' => 'News Blueprint']);

    $this->blueprint->fields()->createMany([
        ['name' => 'Title', 'handle' => 'title', 'type' => 'text', 'is_translatable' => true],
        ['name' => 'Featured', 'handle' => 'is_featured', 'type' => 'boolean', 'is_translatable' => false],
    ]);
});

it('allows admin with manage content permission to view entries index', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.content.collections.entries.index', $this->collection))
        ->assertOk();
});

it('allows admin to create a new dynamic entry', function () {
    $payload = [
        'status' => 'published',
        'blueprint_id' => $this->blueprint->id,
        'data' => [
            'tg' => ['title' => 'Хабарҳои нав'],
            'ru' => ['title' => 'Новые новости'],
            'en' => ['title' => 'New News'],
            'global' => ['is_featured' => true],
        ],
    ];

    $this->actingAs($this->admin)
        ->post(route('admin.content.collections.entries.store', $this->collection), $payload)
        ->assertRedirect();

    $entry = Entry::first();
    expect($entry)->not->toBeNull()
        ->and($entry->status)->toBe(EntryStatus::Published)
        ->and($entry->published_at)->not->toBeNull()
        ->and($entry->data['tg']['title'])->toBe('Хабарҳои нав')
        ->and($entry->data['global']['is_featured'])->toBeTrue();
});
