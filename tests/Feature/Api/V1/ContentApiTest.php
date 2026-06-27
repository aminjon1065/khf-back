<?php

use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Core\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('khf.frontend_api_token', 'test-token');

    $this->collection = Collection::create(['name' => 'Posts', 'slug' => 'posts']);

    $this->blueprint = Blueprint::create([
        'collection_id' => $this->collection->id,
        'name' => 'Default Blueprint',
    ]);

    // Published entry with TG and EN
    $this->publishedEntry = Entry::create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'slug' => 'first-post',
        'status' => 'published',
        'published_at' => now()->subDay(),
        'data' => [
            'tg' => ['title' => 'Хабарҳои нав'],
            'en' => ['title' => 'New News'],
            'global' => ['is_featured' => true],
        ],
    ]);

    // Draft entry
    $this->draftEntry = Entry::create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'slug' => 'draft-post',
        'status' => 'draft',
        'data' => [
            'tg' => ['title' => 'Хабари нотамом'],
        ],
    ]);
});

it('flattens jsonb payload for the requested locale on index', function () {
    $response = $this->withToken('test-token')->getJson('/api/v1/tg/posts');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'first-post')
        ->assertJsonPath('data.0.title', 'Хабарҳои нав')
        ->assertJsonPath('data.0.is_featured', true);
});

it('filters out entries missing the requested locale', function () {
    $response = $this->withToken('test-token')->getJson('/api/v1/ru/posts');

    // 'first-post' doesn't have 'ru' in its data, so the collection should be empty
    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

it('ignores draft entries', function () {
    $response = $this->withToken('test-token')->getJson('/api/v1/tg/posts');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonMissing(['slug' => 'draft-post']);
});

it('flattens jsonb payload for a specific entry', function () {
    $response = $this->withToken('test-token')->getJson('/api/v1/en/posts/first-post');

    $response->assertStatus(200)
        ->assertJsonPath('data.slug', 'first-post')
        ->assertJsonPath('data.title', 'New News')
        ->assertJsonPath('data.is_featured', true);
});

it('returns 404 if specific entry lacks the requested locale', function () {
    $response = $this->withToken('test-token')->getJson('/api/v1/ru/posts/first-post');

    $response->assertStatus(404);
});
