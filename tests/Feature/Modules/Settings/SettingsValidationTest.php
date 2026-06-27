<?php

declare(strict_types=1);

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Settings\Contracts\SettingsManagerInterface;
use App\Modules\Settings\DTOs\SettingDefinition;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Support\SettingsHooks;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->settings = app(SettingsManagerInterface::class);
});

it('rejects a value that violates the type rule', function () {
    $this->settings->register(SettingDefinition::make('general', 'max_items', SettingType::Integer));

    $this->settings->set('general.max_items', 'not-a-number');
})->throws(ValidationException::class);

it('rejects an invalid email', function () {
    $this->settings->register(SettingDefinition::make('email', 'from', SettingType::Email));

    $this->settings->set('email.from', 'not-an-email');
})->throws(ValidationException::class);

it('rejects a malformed hex colour', function () {
    $this->settings->register(SettingDefinition::make('branding', 'accent', SettingType::Color));

    $this->settings->set('branding.accent', 'blue');
})->throws(ValidationException::class);

it('applies the definition\'s own extra rules on top of the type rules', function () {
    $this->settings->register(SettingDefinition::make('general', 'code', SettingType::String, rules: ['max:3']));

    $this->settings->set('general.code', 'toolong');
})->throws(ValidationException::class);

it('accepts a valid value and persists it', function () {
    $this->settings->register(SettingDefinition::make('branding', 'accent', SettingType::Color));

    $this->settings->set('branding.accent', '#FFAA00');

    expect($this->settings->get('branding.accent'))->toBe('#ffaa00');
});

it('honours validation rules injected through the FILTER_RULES hook', function () {
    $this->settings->register(SettingDefinition::make('general', 'nickname', SettingType::String));
    app(HookManagerInterface::class)->addFilter(
        SettingsHooks::FILTER_RULES,
        fn (array $rules): array => [...$rules, 'in:alice,bob'],
    );

    $this->settings->set('general.nickname', 'charlie');
})->throws(ValidationException::class);

it('does not validate unregistered keys', function () {
    $this->settings->set('adhoc.anything', 'literally-anything');

    expect($this->settings->get('adhoc.anything'))->toBe('literally-anything');
});
