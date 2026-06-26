<?php

use App\Models\User;
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

it('lets an editor manage news but blocks settings and users', function (): void {
    $this->actingAs(editorUser())->get('/admin/news')->assertOk();
    $this->actingAs(editorUser())->get('/admin/settings')->assertForbidden();
    $this->actingAs(editorUser())->get('/admin/users')->assertForbidden();
});

it('creates a news item from the admin form', function (): void {
    $this->actingAs(adminUser())->post('/admin/news', [
        'title' => ['ru' => 'Тестовая новость', 'tg' => 'Хабари озмоишӣ', 'en' => 'Test news'],
        'excerpt' => ['ru' => 'Анонс', 'tg' => '', 'en' => ''],
        'body' => ['ru' => '<p>Текст</p>', 'tg' => '', 'en' => ''],
        'slug' => 'admin-test-news',
        'status' => 'published',
        'published_at' => '2026-06-26T09:00',
    ])->assertRedirect('/admin/news');

    $this->assertDatabaseHas('news', ['slug' => 'admin-test-news']);
});
