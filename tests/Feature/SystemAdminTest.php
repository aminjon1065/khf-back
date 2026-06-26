<?php

use App\Models\ContactMessage;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Http::fake();
    $this->seed(RolesAndPermissionsSeeder::class);
});

function sysAdmin(): User
{
    return User::where('email', 'admin@khf.tj')->firstOrFail();
}

function sysEditor(): User
{
    return User::where('email', 'editor@khf.tj')->firstOrFail();
}

it('lists submissions, updates status and deletes', function (): void {
    $report = Report::factory()->create(['status' => 'new']);

    $this->actingAs(sysAdmin())->get('/admin/reports')->assertOk();

    $this->actingAs(sysAdmin())
        ->patch("/admin/reports/{$report->id}/status", ['status' => 'processing'])
        ->assertRedirect();
    $this->assertDatabaseHas('reports', ['id' => $report->id, 'status' => 'processing']);

    $this->actingAs(sysAdmin())->delete("/admin/reports/{$report->id}")->assertRedirect();
    $this->assertDatabaseMissing('reports', ['id' => $report->id]);
});

it('opens messages and subscriptions indexes', function (): void {
    ContactMessage::factory()->create();
    Subscription::factory()->create();

    $this->actingAs(sysAdmin())->get('/admin/messages')->assertOk();
    $this->actingAs(sysAdmin())->get('/admin/subscriptions')->assertOk();
});

it('edits and saves settings', function (): void {
    $this->seed(SettingsSeeder::class);

    $this->actingAs(sysAdmin())->get('/admin/settings')->assertOk();

    $this->actingAs(sysAdmin())->put('/admin/settings', [
        'president' => [
            'name' => 'Тест Президент',
            'role' => ['ru' => 'Роль', 'tg' => '', 'en' => ''],
            'quote' => ['ru' => 'Цитата', 'tg' => '', 'en' => ''],
            'href' => 'https://example.tj',
        ],
        'site_stats' => ['today' => '1', 'month' => '2', 'rescued' => '3', 'reaction' => '4'],
        'forum_stats' => ['members' => '1', 'topics' => '2', 'posts' => '3', 'online' => '4'],
        'map_stats' => ['regions' => 1, 'stations' => 2, 'activeIncidents' => 3, 'monitoring' => '4'],
    ])->assertRedirect();

    expect(Setting::get('president')['name'])->toBe('Тест Президент');
});

it('creates and updates a user with a role', function (): void {
    $this->actingAs(sysAdmin())->get('/admin/users')->assertOk();

    $this->actingAs(sysAdmin())->post('/admin/users', [
        'name' => 'Новый редактор',
        'email' => 'new@khf.tj',
        'password' => 'password123',
        'role' => 'editor',
    ])->assertRedirect('/admin/users');

    $user = User::where('email', 'new@khf.tj')->firstOrFail();
    expect($user->hasRole('editor'))->toBeTrue();

    $this->actingAs(sysAdmin())->put("/admin/users/{$user->id}", [
        'name' => 'Изменён',
        'email' => 'new@khf.tj',
        'role' => 'admin',
        'password' => '',
    ])->assertRedirect('/admin/users');
    expect($user->fresh()->hasRole('admin'))->toBeTrue();
});

it('forbids deleting your own account', function (): void {
    $admin = sysAdmin();
    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertForbidden();
});

it('shows the media library', function (): void {
    $this->actingAs(sysAdmin())->get('/admin/media')->assertOk();
});

it('gates settings and users to admins, allows editor on submissions and media', function (): void {
    $this->actingAs(sysEditor())->get('/admin/reports')->assertOk();
    $this->actingAs(sysEditor())->get('/admin/media')->assertOk();
    $this->actingAs(sysEditor())->get('/admin/settings')->assertForbidden();
    $this->actingAs(sysEditor())->get('/admin/users')->assertForbidden();
});
