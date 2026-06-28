<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocaleRepositoryInterface;
use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Events\LocaleCreated;
use App\Modules\Localization\Events\LocaleDeleted;
use App\Modules\Localization\Events\LocaleUpdated;
use App\Modules\Localization\Events\TranslationCreated;
use App\Modules\Localization\Events\TranslationDeleted;
use App\Modules\Localization\Events\TranslationUpdated;
use App\Modules\Localization\Models\Locale;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Locale::factory()->default()->create(['alias' => 'tj', 'sort_order' => 1]);
    Locale::factory()->create(['code' => 'ru', 'fallback_code' => 'tg', 'sort_order' => 2]);
    Locale::factory()->create(['code' => 'en', 'fallback_code' => 'tg', 'sort_order' => 3]);
});

it('dispatches LocaleCreated when a locale is created through the service', function () {
    // Fake before resolving: the EventBus captures the dispatcher at construction,
    // so faking must precede the first resolution that builds the service graph.
    Event::fake([LocaleCreated::class]);
    $service = app(LocalizationServiceInterface::class);

    $locale = $service->createLocale(new LocaleData(
        code: 'de',
        name: 'German',
        nativeName: 'Deutsch',
        fallbackCode: 'tg',
    ));

    Event::assertDispatched(
        LocaleCreated::class,
        fn (LocaleCreated $e): bool => $e->locale->code === $locale->code && $e->locale->code === 'de',
    );
});

it('dispatches LocaleUpdated when a locale is updated through the service', function () {
    Event::fake([LocaleUpdated::class]);
    $service = app(LocalizationServiceInterface::class);
    $ru = app(LocaleRepositoryInterface::class)->findOrFail('ru');

    $service->updateLocale($ru, new LocaleData(
        code: 'ru',
        name: 'Russian (RU)',
        nativeName: 'Русский',
        fallbackCode: 'tg',
    ));

    Event::assertDispatched(
        LocaleUpdated::class,
        fn (LocaleUpdated $e): bool => $e->locale->code === 'ru' && $e->locale->name === 'Russian (RU)',
    );
});

it('dispatches LocaleDeleted carrying the removed code', function () {
    Event::fake([LocaleDeleted::class]);
    $service = app(LocalizationServiceInterface::class);
    $en = app(LocaleRepositoryInterface::class)->findOrFail('en');

    $service->deleteLocale($en);

    Event::assertDispatched(LocaleDeleted::class, fn (LocaleDeleted $e): bool => $e->code === 'en');
});

it('dispatches TranslationCreated on the first write of a translation', function () {
    Event::fake([TranslationCreated::class, TranslationUpdated::class]);
    $service = app(LocalizationServiceInterface::class);

    $service->setTranslation('ui', 'save', 'tg', 'Захира кардан');

    Event::assertDispatched(
        TranslationCreated::class,
        fn (TranslationCreated $e): bool => $e->translation->group === 'ui'
            && $e->translation->key === 'save'
            && $e->translation->locale === 'tg'
            && $e->translation->value === 'Захира кардан',
    );
    Event::assertNotDispatched(TranslationUpdated::class);
});

it('dispatches TranslationUpdated with the previous value on a subsequent write', function () {
    Event::fake([TranslationUpdated::class]);
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'save', 'ru', 'Сохранить'); // create — TranslationCreated not faked

    $service->setTranslation('ui', 'save', 'ru', 'Сохранять');

    Event::assertDispatched(
        TranslationUpdated::class,
        fn (TranslationUpdated $e): bool => $e->translation->key === 'save'
            && $e->translation->value === 'Сохранять'
            && $e->previous === 'Сохранить',
    );
});

it('dispatches TranslationDeleted only when a row is actually removed', function () {
    Event::fake([TranslationDeleted::class]);
    $service = app(LocalizationServiceInterface::class);
    $service->setTranslation('ui', 'gone', 'tg', 'Рафт');

    $service->forgetTranslation('ui', 'gone', 'tg');
    $service->forgetTranslation('ui', 'gone', 'tg'); // nothing left to delete

    Event::assertDispatchedTimes(TranslationDeleted::class, 1);
    Event::assertDispatched(
        TranslationDeleted::class,
        fn (TranslationDeleted $e): bool => $e->group === 'ui' && $e->key === 'gone' && $e->locale === 'tg',
    );
});
