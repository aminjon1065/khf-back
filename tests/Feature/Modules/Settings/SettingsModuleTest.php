<?php

declare(strict_types=1);

use App\Modules\Settings\Contracts\SettingsCacheInterface;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\Contracts\SettingsRegistryInterface;
use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use App\Modules\Settings\Contracts\SettingsValidatorInterface;
use App\Modules\Settings\Contracts\SettingTypeRegistryInterface;
use App\Modules\Settings\DTOs\SettingGroup;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Facades\Settings;
use App\Modules\Settings\Http\Resources\SettingGroupResource;
use App\Modules\Settings\Http\Resources\SettingResource;
use App\Modules\Settings\ImportExport\ExporterManager;
use App\Modules\Settings\ImportExport\ImporterManager;
use App\Modules\Settings\Models\Setting;
use Illuminate\Support\Facades\Schema;

it('binds every engine contract as a resolvable singleton', function (string $abstract) {
    expect(app($abstract))->toBeObject()
        ->and(app($abstract))->toBe(app($abstract));
})->with([
    SettingsManagerInterface::class,
    SettingsRegistryInterface::class,
    SettingTypeRegistryInterface::class,
    SettingsCacheInterface::class,
    SettingsValidatorInterface::class,
    ExporterManager::class,
    ImporterManager::class,
]);

it('resolves a fresh repository per request', function () {
    expect(app(SettingsRepositoryInterface::class))
        ->not->toBe(app(SettingsRepositoryInterface::class));
});

it('exposes the manager through the Settings facade', function () {
    Settings::set('general.via_facade', 'yes');

    expect(Settings::get('general.via_facade'))->toBe('yes');
});

it('extends the settings table with nullable group and type columns', function () {
    expect(Schema::hasColumns('settings', ['key', 'group', 'type', 'value']))->toBeTrue();
});

it('keeps the legacy Setting model working on the shared table', function () {
    App\Models\Setting::put('president', ['name' => 'X']);

    expect(App\Models\Setting::get('president'))->toBe(['name' => 'X']);
});

it('serializes a persisted setting through SettingResource', function () {
    $setting = Setting::factory()->create([
        'key' => 'general.site_name',
        'group' => 'general',
        'type' => SettingType::String->value,
        'value' => 'KHF',
    ]);

    expect((new SettingResource($setting))->toArray(request()))
        ->toMatchArray([
            'key' => 'general.site_name',
            'group' => 'general',
            'type' => SettingType::String->value,
            'value' => 'KHF',
        ]);
});

it('serializes a group through SettingGroupResource', function () {
    $resource = new SettingGroupResource(new SettingGroup('seo', 'SEO', 'Search engine options'));

    expect($resource->toArray(request()))
        ->toBe(['name' => 'seo', 'label' => 'SEO', 'description' => 'Search engine options']);
});
