<?php

use App\Core\Enums\FieldType;
use App\Core\Exceptions\CoreException;
use App\Core\Schema\FieldTypeRegistry;
use App\Core\Schema\FieldTypes\BooleanFieldType;
use App\Core\Schema\FieldTypes\TextFieldType;

it('registers and resolves a field type', function () {
    $registry = new FieldTypeRegistry;
    $registry->register(new TextFieldType);

    expect($registry->has(FieldType::Text))->toBeTrue()
        ->and($registry->get(FieldType::Text))->toBeInstanceOf(TextFieldType::class);
});

it('reports unregistered types as absent', function () {
    $registry = new FieldTypeRegistry;

    expect($registry->has(FieldType::Boolean))->toBeFalse();
});

it('throws when resolving an unregistered type', function () {
    (new FieldTypeRegistry)->get(FieldType::Media);
})->throws(CoreException::class);

it('returns all registered types keyed by backing value', function () {
    $registry = new FieldTypeRegistry;
    $registry->register(new TextFieldType);
    $registry->register(new BooleanFieldType);

    expect($registry->all())->toHaveCount(2)
        ->and(array_keys($registry->all()))->toBe(['text', 'boolean']);
});

it('resolves field-type defaults from the enum', function () {
    $text = new TextFieldType;

    expect($text->type())->toBe(FieldType::Text)
        ->and($text->isTranslatableByDefault())->toBeTrue()
        ->and($text->defaultValidationRules())->toBe(['string', 'max:255']);

    expect((new BooleanFieldType)->isTranslatableByDefault())->toBeFalse();
});
