<?php

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Http::fake();
    $this->seed(RolesAndPermissionsSeeder::class);
});

function adminUser(): User
{
    return User::where('email', 'admin@khf.tj')->firstOrFail();
}

function editorUser(): User
{
    return User::where('email', 'editor@khf.tj')->firstOrFail();
}

it('redirects guests away from the admin', function (): void {
    $this->get('/admin')->assertRedirect('/login');
});

it('forbids users without an admin role', function (): void {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});

it('lets an admin open the dashboard', function (): void {
    $this->actingAs(adminUser())->get('/admin')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('admin/dashboard'));
});

it('lets an editor manage content but blocks settings and users', function (): void {
    $this->seed(ContentSeeder::class);
    $collection = Collection::where('slug', 'news')->first();
    $this->actingAs(editorUser())->get("/admin/collections/{$collection->id}/entries")->assertOk();
    $this->actingAs(editorUser())->get('/admin/settings')->assertForbidden();
    $this->actingAs(editorUser())->get('/admin/users')->assertForbidden();
});

it('creates a dynamic entry from the admin form', function (): void {
    $this->seed(ContentSeeder::class);
    $collection = Collection::where('slug', 'news')->first();
    $blueprintId = $collection->blueprints()->first()->id;

    $this->actingAs(adminUser())->post("/admin/collections/{$collection->id}/entries", [
        'blueprint_id' => $blueprintId,
        'slug' => 'admin-test-news',
        'status' => 'published',
        'published_at' => '2026-06-26T09:00',
        'data' => [
            'ru' => ['title' => 'Тестовая новость', 'body' => '<p>Текст</p>'],
            'tg' => ['title' => 'Хабари озмоишӣ', 'body' => ''],
            'en' => ['title' => 'Test news', 'body' => ''],
        ],
    ])->assertRedirect("/admin/collections/{$collection->id}/entries");

    $this->assertDatabaseHas('entries', ['collection_id' => $collection->id]);
    $entry = Entry::where('collection_id', $collection->id)->first();
    expect($entry->data['ru']['title'])->toBe('Тестовая новость');
});
