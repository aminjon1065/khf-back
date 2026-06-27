<?php

declare(strict_types=1);

use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\Contracts\SettingsRegistryInterface;
use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\DTOs\SettingGroup;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Exceptions\SettingsException;
use App\Modules\Settings\Models\Setting;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->settings = app(SettingsManagerInterface::class);
});

it('returns a registered default before any override is stored', function () {
    $this->settings->register(SettingDefinition::make('general', 'site_name', SettingType::String, 'KHF'));

    expect($this->settings->get('general.site_name'))->toBe('KHF')
        ->and($this->settings->has('general.site_name'))->toBeTrue();
    $this->assertDatabaseMissing('settings', ['key' => 'general.site_name']);
});

it('persists and reads back an override, casting to the declared type', function () {
    $this->settings->register(SettingDefinition::make('general', 'max_items', SettingType::Integer, 10));

    $this->settings->set('general.max_items', '25');

    expect($this->settings->get('general.max_items'))->toBe(25);
    $this->assertDatabaseHas('settings', [
        'key' => 'general.max_items',
        'group' => 'general',
        'type' => SettingType::Integer->value,
    ]);
});

it('round-trips a date through serialize and cast', function () {
    $this->settings->register(SettingDefinition::make('general', 'launch_date', SettingType::Date));

    $this->settings->set('general.launch_date', '2026-06-27 18:00:00');

    $value = $this->settings->get('general.launch_date');
    expect($value)->toBeInstanceOf(CarbonImmutable::class)
        ->and($value->format('Y-m-d'))->toBe('2026-06-27');
    $this->assertDatabaseHas('settings', ['key' => 'general.launch_date', 'value' => json_encode('2026-06-27')]);
});

it('stores unregistered keys untyped and without validation', function () {
    $this->settings->set('adhoc.flag', ['anything' => true]);

    expect($this->settings->get('adhoc.flag'))->toBe(['anything' => true]);
    $this->assertDatabaseHas('settings', ['key' => 'adhoc.flag', 'group' => null, 'type' => null]);
});

it('forgets an override and falls back to the registered default', function () {
    $this->settings->register(SettingDefinition::make('general', 'tagline', SettingType::String, 'default-tagline'));
    $this->settings->set('general.tagline', 'custom');
    expect($this->settings->get('general.tagline'))->toBe('custom');

    $this->settings->forget('general.tagline');

    expect($this->settings->get('general.tagline'))->toBe('default-tagline');
    $this->assertDatabaseMissing('settings', ['key' => 'general.tagline']);
});

it('returns the explicit fallback for a fully unknown key', function () {
    expect($this->settings->get('nope.nope', 'fallback'))->toBe('fallback')
        ->and($this->settings->has('nope.nope'))->toBeFalse();
});

it('overlays stored values on top of registered defaults in all()', function () {
    $this->settings->register(SettingDefinition::make('general', 'a', SettingType::String, 'da'));
    $this->settings->register(SettingDefinition::make('general', 'b', SettingType::Integer, 1));
    $this->settings->set('general.b', 5);

    expect($this->settings->all())
        ->toMatchArray(['general.a' => 'da', 'general.b' => 5]);
});

it('resolves only the requested group via forGroup()', function () {
    $this->settings->register(SettingDefinition::make('seo', 'title', SettingType::String, 'T'));
    $this->settings->register(SettingDefinition::make('seo', 'desc', SettingType::String, 'D'));
    $this->settings->register(SettingDefinition::make('email', 'from', SettingType::Email, 'a@b.c'));

    expect($this->settings->forGroup('seo'))
        ->toBe(['seo.title' => 'T', 'seo.desc' => 'D']);
});

it('auto-registers a bare group when a definition references an unknown one', function () {
    $registry = app(SettingsRegistryInterface::class);
    $this->settings->register(SettingDefinition::make('custom_group', 'x', SettingType::String));

    expect($registry->groups())->toHaveKey('custom_group');
});

it('exposes the eleven default groups registered at boot', function () {
    $groups = app(SettingsRegistryInterface::class)->groups();

    expect(array_keys($groups))->toContain(
        'general', 'branding', 'localization', 'media', 'seo',
        'email', 'security', 'api', 'search', 'workflow', 'system',
    );
});

it('registers a custom group through the manager', function () {
    $this->settings->registerGroup(new SettingGroup('reports', 'Reports', 'Reporting options'));

    expect(app(SettingsRegistryInterface::class)->groups())->toHaveKey('reports');
});

it('writes the persisted value through the engine Setting model', function () {
    $this->settings->register(SettingDefinition::make('general', 'k', SettingType::String, 'd'));
    $this->settings->set('general.k', 'v');

    $row = Setting::query()->where('key', 'general.k')->firstOrFail();
    expect($row->value)->toBe('v')
        ->and($row->group)->toBe('general')
        ->and($row->type)->toBe(SettingType::String->value);
});

it('preserves existing engine metadata when an untyped write targets a previously-typed row', function () {
    $repository = app(SettingsRepositoryInterface::class);
    $repository->put('general.kept', 'general', SettingType::Integer->value, 1);

    // An untyped-path write (null group/type) must not strip the classification.
    $repository->put('general.kept', null, null, 2);

    $row = Setting::query()->where('key', 'general.kept')->firstOrFail();
    expect($row->value)->toBe(2)
        ->and($row->group)->toBe('general')
        ->and($row->type)->toBe(SettingType::Integer->value);
});

it('refuses to write a bare (non-namespaced) key', function () {
    $this->settings->set('barekey', 'x');
})->throws(SettingsException::class);

it('refuses to forget a bare (non-namespaced) key, protecting legacy singletons', function () {
    App\Models\Setting::put('president', ['name' => 'X']);

    try {
        $this->settings->forget('president');
        $this->fail('Expected a SettingsException for a bare key.');
    } catch (SettingsException) {
        // The legacy singleton must remain untouched.
        expect(App\Models\Setting::get('president'))->toBe(['name' => 'X']);
    }
});
