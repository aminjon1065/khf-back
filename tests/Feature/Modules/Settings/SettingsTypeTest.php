<?php

declare(strict_types=1);

use App\Modules\Settings\Contracts\SettingTypeRegistryInterface;
use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Exceptions\UnknownSettingTypeException;
use Carbon\CarbonImmutable;

function settingType(string $name)
{
    return app(SettingTypeRegistryInterface::class)->get($name);
}

it('registers every native type at boot', function () {
    $registry = app(SettingTypeRegistryInterface::class);

    foreach (SettingType::values() as $name) {
        expect($registry->has($name))->toBeTrue();
    }
});

it('throws for an unknown type name', function () {
    app(SettingTypeRegistryInterface::class)->get('does-not-exist');
})->throws(UnknownSettingTypeException::class);

it('serializes and casts integers', function () {
    $type = settingType(SettingType::Integer->value);

    expect($type->serialize('42'))->toBe(42)
        ->and($type->cast('42'))->toBe(42);
});

it('serializes and casts booleans from loose input', function () {
    $type = settingType(SettingType::Boolean->value);

    expect($type->serialize('true'))->toBeTrue()
        ->and($type->serialize('0'))->toBeFalse()
        ->and($type->cast(1))->toBeTrue();
});

it('normalises a date to Y-m-d on the way in and a CarbonImmutable on the way out', function () {
    $type = settingType(SettingType::Date->value);

    expect($type->serialize('2026-06-27 13:45:00'))->toBe('2026-06-27')
        ->and($type->cast('2026-06-27'))->toBeInstanceOf(CarbonImmutable::class);
});

it('lower-cases colours', function () {
    $type = settingType(SettingType::Color->value);

    expect($type->serialize('#AABBCC'))->toBe('#aabbcc');
});

it('coerces arrays', function () {
    $type = settingType(SettingType::Arr->value);

    expect($type->serialize(['a', 'b']))->toBe(['a', 'b'])
        ->and($type->serialize('x'))->toBe(['x']);
});

it('extracts a media key from an object that exposes getKey()', function () {
    $type = settingType(SettingType::Media->value);

    $media = new class
    {
        public function getKey(): string
        {
            return 'media-uuid-123';
        }
    };

    expect($type->serialize($media))->toBe('media-uuid-123')
        ->and($type->serialize('plain-id'))->toBe('plain-id');
});

it('passes null through every native type unchanged', function () {
    foreach (SettingType::values() as $name) {
        $type = settingType($name);
        expect($type->serialize(null))->toBeNull()
            ->and($type->cast(null))->toBeNull();
    }
});
