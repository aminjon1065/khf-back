<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Events\FallbackUsed;
use App\Modules\Localization\Models\Locale;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Locale::factory()->default()->create(['alias' => 'tj', 'sort_order' => 1]);
    Locale::factory()->create(['code' => 'ru', 'fallback_code' => 'tg', 'sort_order' => 2]);
    Locale::factory()->create(['code' => 'en', 'fallback_code' => 'tg', 'sort_order' => 3]);
});

it('falls back to the default locale via the chain when a value is missing', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'save', 'tg', 'Захира кардан');

    expect($service->translate('ui', 'save', 'ru'))->toBe('Захира кардан');
});

it('prefers the explicit locale value over the fallback', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'save', 'tg', 'Захира кардан');
    $service->setTranslation('ui', 'save', 'ru', 'Сохранить');

    expect($service->translate('ui', 'save', 'ru'))->toBe('Сохранить');
});

it('returns null under the strict strategy when the requested locale lacks the key', function () {
    config()->set('khf.localization.fallback_strategy', 'strict');
    config()->set('khf.localization.missing_translation', 'null');
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'save', 'tg', 'Захира кардан');

    expect($service->translate('ui', 'save', 'ru'))->toBeNull();
});

it('returns null for a missing translation when missing_translation is null', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    config()->set('khf.localization.missing_translation', 'null');
    $service = app(LocalizationServiceInterface::class);

    expect($service->translate('ui', 'absent', 'tg'))->toBeNull();
});

it('returns the full key for a missing translation when missing_translation is key', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    config()->set('khf.localization.missing_translation', 'key');
    $service = app(LocalizationServiceInterface::class);

    expect($service->translate('ui', 'absent', 'tg'))->toBe('ui.absent');
});

it('returns an empty string for a missing translation when missing_translation is empty', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    config()->set('khf.localization.missing_translation', 'empty');
    $service = app(LocalizationServiceInterface::class);

    expect($service->translate('ui', 'absent', 'tg'))->toBe('');
});

it('dispatches FallbackUsed when a fallback locale answers', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    Event::fake([FallbackUsed::class]);
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'save', 'tg', 'Захира кардан');

    $service->translate('ui', 'save', 'ru');

    Event::assertDispatched(
        FallbackUsed::class,
        fn (FallbackUsed $e): bool => $e->requestedLocale === 'ru'
            && $e->resolvedLocale === 'tg'
            && $e->context === 'ui.save',
    );
});

it('does not dispatch FallbackUsed when the requested locale answers', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    Event::fake([FallbackUsed::class]);
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'save', 'ru', 'Сохранить');

    $service->translate('ui', 'save', 'ru');

    Event::assertNotDispatched(FallbackUsed::class);
});

it('resolves a locale-keyed value map through the fallback chain', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    $service = app(LocalizationServiceInterface::class);

    $resolved = $service->resolve(['tg' => 'Унвон', 'en' => 'Title'], 'ru', 'entry.title');

    expect($resolved)->toBe('Унвон');
});

it('dispatches FallbackUsed when resolving a value map via fallback', function () {
    config()->set('khf.localization.fallback_strategy', 'chain');
    Event::fake([FallbackUsed::class]);
    $service = app(LocalizationServiceInterface::class);

    $service->resolve(['tg' => 'Унвон'], 'ru', 'entry.title');

    Event::assertDispatched(
        FallbackUsed::class,
        fn (FallbackUsed $e): bool => $e->requestedLocale === 'ru'
            && $e->resolvedLocale === 'tg'
            && $e->context === 'entry.title',
    );
});
