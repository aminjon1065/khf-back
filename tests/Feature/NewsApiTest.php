<?php

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
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
    News::factory()->create();

    $this->getJson('/api/v1/news')->assertUnauthorized();
});

it('returns published news with pagination meta', function (): void {
    News::factory()->count(3)->create();
    News::factory()->draft()->create();

    $this->withHeaders(frontendHeaders())
        ->getJson('/api/v1/news')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.total', 3)
        ->assertJsonStructure([
            'data' => [['id', 'slug', 'category', 'categoryColor', 'tone', 'title', 'excerpt', 'body', 'date', 'views', 'image']],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
});

it('localizes content by Accept-Language', function (): void {
    $category = NewsCategory::factory()->create([
        'name' => ['tg' => 'Наҷот', 'ru' => 'Спасение', 'en' => 'Rescue'],
    ]);
    News::factory()->create([
        'news_category_id' => $category->id,
        'slug' => 'demo',
        'title' => ['tg' => 'Сарлавҳа', 'ru' => 'Заголовок', 'en' => 'Title'],
    ]);

    $this->withHeaders(frontendHeaders('ru'))->getJson('/api/v1/news/demo')
        ->assertOk()
        ->assertJsonPath('data.title', 'Заголовок')
        ->assertJsonPath('data.category', 'Спасение');

    $this->withHeaders(frontendHeaders('tg'))->getJson('/api/v1/news/demo')
        ->assertOk()
        ->assertJsonPath('data.title', 'Сарлавҳа')
        ->assertJsonPath('data.category', 'Наҷот');
});

it('hides drafts and returns 404 for unknown slug', function (): void {
    News::factory()->draft()->create(['slug' => 'hidden']);

    $this->withHeaders(frontendHeaders())->getJson('/api/v1/news/hidden')->assertNotFound();
    $this->withHeaders(frontendHeaders())->getJson('/api/v1/news/nope')->assertNotFound();
});

it('triggers Next revalidation when news is saved', function (): void {
    config([
        'khf.revalidate.url' => 'http://localhost:3000/api/revalidate',
        'khf.revalidate.secret' => 'sec',
    ]);
    Http::fake();

    News::factory()->create(['slug' => 'fresh']);

    Http::assertSent(function ($request): bool {
        return str_contains($request->url(), '/api/revalidate')
            && in_array('news', $request['tags'] ?? [], true);
    });
});
