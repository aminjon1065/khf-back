<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Exceptions\SettingsException;

beforeEach(function () {
    $this->settings = app(SettingsManagerInterface::class);
});

it('exports only persisted overrides as pretty JSON', function () {
    $this->settings->register(SettingDefinition::make('general', 'site_name', SettingType::String, 'KHF'));
    $this->settings->set('general.site_name', 'KHF CMS');

    $json = $this->settings->export('json');
    $decoded = json_decode($json, true);

    // Defaults that were never overridden must not appear in the export.
    expect($decoded)->toBe(['general.site_name' => 'KHF CMS']);
});

it('excludes legacy singleton rows from the export', function () {
    Setting::put('president', ['name' => 'X']);
    $this->settings->set('general.real', 'r');

    $decoded = json_decode($this->settings->export('json'), true);

    expect($decoded)->toHaveKey('general.real')
        ->and($decoded)->not->toHaveKey('president');
});

it('round-trips persisted overrides through export then import', function () {
    $this->settings->register(SettingDefinition::make('general', 'launch_date', SettingType::Date));
    $this->settings->register(SettingDefinition::make('general', 'max_items', SettingType::Integer, 0));
    $this->settings->set('general.launch_date', '2026-06-27');
    $this->settings->set('general.max_items', 9);

    $payload = $this->settings->export('json');
    $this->settings->forget('general.launch_date');
    $this->settings->forget('general.max_items');

    $keys = $this->settings->import($payload, 'json');

    expect($keys)->toEqualCanonicalizing(['general.launch_date', 'general.max_items'])
        ->and($this->settings->get('general.max_items'))->toBe(9)
        ->and($this->settings->get('general.launch_date')->format('Y-m-d'))->toBe('2026-06-27');
});

it('imports values from a JSON payload', function () {
    $keys = $this->settings->import(json_encode([
        'general.a' => 'one',
        'general.b' => 'two',
    ]), 'json');

    expect($keys)->toBe(['general.a', 'general.b'])
        ->and($this->settings->get('general.a'))->toBe('one');
    $this->assertDatabaseHas('settings', ['key' => 'general.a', 'value' => json_encode('one')]);
});

it('throws when importing a non-object JSON payload', function () {
    $this->settings->import(json_encode(['just', 'a', 'list']), 'json');
})->throws(SettingsException::class);

it('throws for an unsupported export format', function () {
    $this->settings->export('yaml');
})->throws(SettingsException::class);

it('throws for an unsupported import format', function () {
    $this->settings->import('{}', 'xml');
})->throws(SettingsException::class);
