<?php

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(ContentSeeder::class);
    Http::fake(); // не дёргаем реальный Next при сохранении новостей
    config(['khf.frontend_api_token' => 'test-token']);
});

/**
 * @return array<string, string>
 */
function frontendHeaders(string $locale = 'ru'): array
{
    return [
        'Authorization' => 'Bearer test-token',
        'Accept-Language' => $locale,
    ];
}

it('rejects requests without the frontend token', function (): void {
    $this->getJson('/api/v1/ru/news')->assertUnauthorized();
});

it('returns published news with pagination meta', function (): void {
    $collection = Collection::where('slug', 'news')->first();
    $blueprintId = $collection->blueprints()->first()->id;
    Entry::create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprintId,
        'status' => 'published',
        'published_at' => now(),
        'slug' => 'n1',
        'data' => ['ru' => ['title' => 'Test 1']],
    ]);
    Entry::create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprintId,
        'status' => 'published',
        'published_at' => now(),
        'slug' => 'n2',
        'data' => ['ru' => ['title' => 'Test 2']],
    ]);
    Entry::create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprintId,
        'status' => 'published',
        'published_at' => now(),
        'slug' => 'n3',
        'data' => ['ru' => ['title' => 'Test 3']],
    ]);

    $this->withHeaders(frontendHeaders())
        ->getJson('/api/v1/ru/news')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.total', 3)
        ->assertJsonStructure([
            'data' => [['id', 'slug', 'category', 'categoryColor', 'tone', 'title', 'excerpt', 'body', 'date', 'views', 'image']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
});

it('localizes content by Accept-Language', function (): void {
    $catColl = Collection::where('slug', 'news-categories')->first();
    $catBpId = $catColl->blueprints()->first()->id;
    $cat = Entry::create([
        'collection_id' => $catColl->id,
        'blueprint_id' => $catBpId,
        'status' => 'published',
        'slug' => 'cat1',
        'data' => ['tg' => ['title' => 'Наҷот'], 'ru' => ['title' => 'Спасение'], 'en' => ['title' => 'Rescue']],
    ]);

    $newsColl = Collection::where('slug', 'news')->first();
    $newsBpId = $newsColl->blueprints()->first()->id;
    Entry::create([
        'collection_id' => $newsColl->id,
        'blueprint_id' => $newsBpId,
        'status' => 'published',
        'published_at' => now(),
        'slug' => 'demo',
        'data' => [
            'global' => ['category_id' => $cat->id],
            'tg' => ['title' => 'Сарлавҳа'],
            'ru' => ['title' => 'Заголовок'],
            'en' => ['title' => 'Title'],
        ],
    ]);

    $this->withHeaders(frontendHeaders('ru'))->getJson('/api/v1/ru/news/demo')
        ->assertOk()
        ->assertJsonPath('data.title', 'Заголовок')
        ->assertJsonPath('data.category', 'Спасение');

    $this->withHeaders(frontendHeaders('tg'))->getJson('/api/v1/tg/news/demo')
        ->assertOk()
        ->assertJsonPath('data.title', 'Сарлавҳа')
        ->assertJsonPath('data.category', 'Наҷот');
});

it('hides drafts and returns 404 for unknown slug', function (): void {
    $collection = Collection::where('slug', 'news')->first();
    $blueprintId = $collection->blueprints()->first()->id;
    Entry::create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprintId,
        'status' => 'draft',
        'slug' => 'hidden',
        'data' => [],
    ]);

    $this->withHeaders(frontendHeaders())->getJson('/api/v1/ru/news/hidden')->assertNotFound();
    $this->withHeaders(frontendHeaders())->getJson('/api/v1/ru/news/nope')->assertNotFound();
});

it('triggers Next revalidation when news is saved', function (): void {
    config([
        'khf.revalidate.url' => 'http://localhost:3000/api/revalidate',
        'khf.revalidate.secret' => 'sec',
    ]);
    Http::fake();

    $collection = Collection::where('slug', 'news')->first();
    $blueprintId = $collection->blueprints()->first()->id;
    Entry::create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprintId,
        'status' => 'published',
        'slug' => 'fresh',
        'data' => [],
    ]);
});
