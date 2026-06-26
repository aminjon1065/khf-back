<?php

use Database\Seeders\ActivitiesSeeder;
use Database\Seeders\ContactsSeeder;
use Database\Seeders\DocumentSeeder;
use Database\Seeders\ForumSeeder;
use Database\Seeders\HomeSeeder;
use Database\Seeders\NewsSeeder;
use Database\Seeders\RegionSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\StructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Http::fake();
    config(['khf.frontend_api_token' => 'test-token']);
});

/**
 * @return array<string, string>
 */
function feHeaders(string $locale = 'ru'): array
{
    return ['Authorization' => 'Bearer test-token', 'Accept-Language' => $locale];
}

it('serves every content section to the frontend', function (): void {
    $this->seed([
        SettingsSeeder::class,
        NewsSeeder::class,
        DocumentSeeder::class,
        StructureSeeder::class,
        ActivitiesSeeder::class,
        ForumSeeder::class,
        RegionSeeder::class,
        ContactsSeeder::class,
        HomeSeeder::class,
    ]);

    $h = feHeaders();

    $this->withHeaders($h)->getJson('/api/v1/structure')->assertOk()
        ->assertJsonStructure(['data' => ['leadership', 'departments', 'offices']]);
    $this->withHeaders($h)->getJson('/api/v1/activities')->assertOk()
        ->assertJsonStructure(['data' => ['directions', 'programs']]);
    $this->withHeaders($h)->getJson('/api/v1/documents')->assertOk()
        ->assertJsonStructure(['data' => ['categories', 'items']]);
    $this->withHeaders($h)->getJson('/api/v1/forum')->assertOk()
        ->assertJsonStructure(['data' => ['categories', 'topics', 'stats']]);
    $this->withHeaders($h)->getJson('/api/v1/regions')->assertOk()
        ->assertJsonStructure(['data' => ['regions', 'stats']]);
    $this->withHeaders($h)->getJson('/api/v1/contacts')->assertOk()
        ->assertJsonStructure(['data' => ['hotlines', 'headOffice', 'offices']]);
    $this->withHeaders($h)->getJson('/api/v1/home')->assertOk()
        ->assertJsonStructure(['data' => ['services', 'president', 'stats']]);
    $this->withHeaders($h)->getJson('/api/v1/home/slides')->assertOk()
        ->assertJsonStructure(['data']);
});

it('localizes the president by Accept-Language', function (): void {
    $this->seed(SettingsSeeder::class);

    $ru = $this->withHeaders(feHeaders('ru'))->getJson('/api/v1/home')->json('data.president.role');
    $tg = $this->withHeaders(feHeaders('tg'))->getJson('/api/v1/home')->json('data.president.role');

    expect($ru)->toBe('Президент Республики Таджикистан');
    expect($tg)->toBe('Президенти Ҷумҳурии Тоҷикистон');
});

it('accepts a report submission and stores it', function (): void {
    $payload = [
        'type' => 'Сӯхтор',
        'region' => 'Душанбе',
        'location' => 'Лоҳутӣ 26',
        'description' => 'тест',
        'phone' => '+992001112233',
    ];

    $this->withHeaders(feHeaders())->postJson('/api/v1/reports', $payload)
        ->assertCreated()
        ->assertJsonPath('ok', true)
        ->assertJsonStructure(['ok', 'reference']);

    $this->assertDatabaseHas('reports', ['region' => 'Душанбе', 'type' => 'Сӯхтор']);
});

it('validates the contact form', function (): void {
    $this->withHeaders(feHeaders())->postJson('/api/v1/contact', [])->assertStatus(422);

    $this->withHeaders(feHeaders())->postJson('/api/v1/contact', [
        'name' => 'Тест',
        'email' => 'a@b.tj',
        'message' => 'Привет',
    ])->assertCreated()->assertJsonPath('ok', true);

    $this->assertDatabaseHas('contact_messages', ['email' => 'a@b.tj']);
});
