<?php

use App\Core\Contracts\Schema\FieldTypeRegistryInterface;
use App\Core\Contracts\Schema\SchemaEngineInterface;
use App\Core\Enums\FieldType;
use App\Core\Schema\SchemaEngine;

it('binds the schema engine façade as a singleton', function () {
    expect(app(SchemaEngineInterface::class))
        ->toBeInstanceOf(SchemaEngine::class)
        ->toBe(app(SchemaEngineInterface::class));
});

it('seeds the registry with all twelve built-in field types', function () {
    $registry = app(FieldTypeRegistryInterface::class);

    foreach (FieldType::cases() as $case) {
        expect($registry->has($case))->toBeTrue();
    }

    expect($registry->all())->toHaveCount(count(FieldType::cases()));
});

it('exposes the field type registry through the engine', function () {
    $engine = app(SchemaEngineInterface::class);

    expect($engine->fieldTypes())->toBe(app(FieldTypeRegistryInterface::class));
});
