<?php

declare(strict_types=1);

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Events\SettingCreated;
use App\Modules\Settings\Events\SettingDeleted;
use App\Modules\Settings\Events\SettingsExported;
use App\Modules\Settings\Events\SettingsImported;
use App\Modules\Settings\Events\SettingUpdated;
use App\Modules\Settings\Support\SettingsHooks;
use Illuminate\Support\Facades\Event;

it('dispatches SettingCreated on the first write of a key', function () {
    Event::fake([SettingCreated::class, SettingUpdated::class]);
    $settings = app(SettingsManagerInterface::class);
    $settings->register(SettingDefinition::make('general', 'site_name', SettingType::String, 'KHF'));

    $settings->set('general.site_name', 'KHF CMS');

    Event::assertDispatched(SettingCreated::class, fn (SettingCreated $e): bool => $e->key === 'general.site_name' && $e->value === 'KHF CMS');
    Event::assertNotDispatched(SettingUpdated::class);
});

it('dispatches SettingUpdated with the previous value on a subsequent write', function () {
    // Fake before resolving the manager: the event bus captures the dispatcher at
    // construction, so faking must precede the first resolution that builds it.
    Event::fake([SettingUpdated::class]);
    $settings = app(SettingsManagerInterface::class);
    $settings->register(SettingDefinition::make('general', 'count', SettingType::Integer, 0));
    $settings->set('general.count', 1); // create — SettingCreated is not faked here
    $settings->set('general.count', 2);

    Event::assertDispatched(SettingUpdated::class, fn (SettingUpdated $e): bool => $e->key === 'general.count' && $e->value === 2 && $e->previous === 1);
});

it('does not re-announce a write of an unchanged value', function () {
    Event::fake([SettingUpdated::class]);
    $settings = app(SettingsManagerInterface::class);
    $settings->set('adhoc.same', 'v'); // create (SettingUpdated not faked here)

    $settings->set('adhoc.same', 'v'); // identical → no-op short-circuit

    Event::assertNotDispatched(SettingUpdated::class);
    expect($settings->get('adhoc.same'))->toBe('v');
});

it('dispatches SettingDeleted only when a row is actually removed', function () {
    Event::fake([SettingDeleted::class]);
    $settings = app(SettingsManagerInterface::class);
    $settings->set('adhoc.k', 'v');

    $settings->forget('adhoc.k');
    $settings->forget('adhoc.k'); // nothing left to delete

    Event::assertDispatchedTimes(SettingDeleted::class, 1);
});

it('fires the CHANGED hook with key, new and previous values', function () {
    $settings = app(SettingsManagerInterface::class);
    $captured = [];
    app(HookManagerInterface::class)->addAction(
        SettingsHooks::CHANGED,
        function (string $key, mixed $value, mixed $previous) use (&$captured): void {
            $captured = [$key, $value, $previous];
        },
    );

    $settings->set('adhoc.toggle', 'on');

    expect($captured)->toBe(['adhoc.toggle', 'on', null]);
});

it('dispatches SettingsExported with a count and format', function () {
    Event::fake([SettingsExported::class]);
    $settings = app(SettingsManagerInterface::class);
    $settings->set('adhoc.a', '1');
    $settings->set('adhoc.b', '2');

    $settings->export('json');

    Event::assertDispatched(SettingsExported::class, fn (SettingsExported $e): bool => $e->count === 2 && $e->format === 'json');
});

it('dispatches SettingsImported with the imported keys', function () {
    Event::fake([SettingsImported::class]);
    $settings = app(SettingsManagerInterface::class);

    $settings->import(json_encode(['adhoc.a' => '1', 'adhoc.b' => '2']), 'json');

    Event::assertDispatched(SettingsImported::class, fn (SettingsImported $e): bool => $e->format === 'json' && $e->keys === ['adhoc.a', 'adhoc.b']);
});
