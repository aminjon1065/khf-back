<?php

use Database\Seeders\ContentSeeder;
use Database\Seeders\SettingsSeeder;
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
        ContentSeeder::class,
    ]);

    $h = feHeaders();

    $this->withHeaders($h)->getJson('/api/v1/ru/structure')->assertOk()
        ->assertJsonStructure(['data' => ['leadership', 'departments', 'offices']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/activities')->assertOk()
        ->assertJsonStructure(['data' => ['directions', 'programs']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/documents')->assertOk()
        ->assertJsonStructure(['data' => ['categories', 'items']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/forum')->assertOk()
        ->assertJsonStructure(['data' => ['categories', 'topics', 'stats']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/regions')->assertOk()
        ->assertJsonStructure(['data' => ['regions', 'stats']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/contacts')->assertOk()
        ->assertJsonStructure(['data' => ['hotlines', 'headOffice', 'offices']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/home')->assertOk()
        ->assertJsonStructure(['data' => ['services', 'president', 'stats']]);
    $this->withHeaders($h)->getJson('/api/v1/ru/home/slides')->assertOk()
        ->assertJsonStructure(['data']);
});

it('localizes the president by URL locale', function (): void {
    $this->seed(SettingsSeeder::class);

    $ru = $this->withHeaders(feHeaders('ru'))->getJson('/api/v1/ru/home')->json('data.president.role');
    $tg = $this->withHeaders(feHeaders('tg'))->getJson('/api/v1/tg/home')->json('data.president.role');

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

    $this->withHeaders(feHeaders())->postJson('/api/v1/ru/reports', $payload)
        ->assertCreated()
        ->assertJsonPath('ok', true)
        ->assertJsonStructure(['ok', 'reference']);

    $this->assertDatabaseHas('reports', ['region' => 'Душанбе', 'type' => 'Сӯхтор']);
});

it('validates the contact form', function (): void {
    $this->withHeaders(feHeaders())->postJson('/api/v1/ru/contact', [])->assertStatus(422);

    $this->withHeaders(feHeaders())->postJson('/api/v1/ru/contact', [
        'name' => 'Тест',
        'email' => 'a@b.tj',
        'message' => 'Привет',
    ])->assertCreated()->assertJsonPath('ok', true);

    $this->assertDatabaseHas('contact_messages', ['email' => 'a@b.tj']);
});
