<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocaleRepositoryInterface;
use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Enums\TextDirection;
use App\Modules\Localization\Exceptions\DuplicateLocaleException;
use App\Modules\Localization\Exceptions\LocaleNotFoundException;
use App\Modules\Localization\Models\Locale;

beforeEach(function () {
    Locale::factory()->default()->create(['alias' => 'tj', 'sort_order' => 1]);
    Locale::factory()->create(['code' => 'ru', 'fallback_code' => 'tg', 'sort_order' => 2]);
    Locale::factory()->create(['code' => 'en', 'fallback_code' => 'tg', 'sort_order' => 3]);

    $this->service = app(LocalizationServiceInterface::class);
    $this->locales = app(LocaleRepositoryInterface::class);
});

it('creates a locale and persists every column', function () {
    $locale = $this->service->createLocale(new LocaleData(
        code: 'de',
        name: 'German',
        nativeName: 'Deutsch',
        direction: TextDirection::Ltr,
        isDefault: false,
        isActive: true,
        fallbackCode: 'tg',
        alias: null,
        sortOrder: 4,
    ));

    expect($locale)->toBeInstanceOf(Locale::class)
        ->and($locale->code)->toBe('de')
        ->and($locale->name)->toBe('German')
        ->and($locale->native_name)->toBe('Deutsch')
        ->and($locale->fallback_code)->toBe('tg');

    $this->assertDatabaseHas('locales', ['code' => 'de', 'name' => 'German', 'fallback_code' => 'tg']);
});

it('updates a locale through the service', function () {
    $ru = $this->locales->findOrFail('ru');

    $updated = $this->service->updateLocale($ru, new LocaleData(
        code: 'ru',
        name: 'Russian (RU)',
        nativeName: 'Русский',
        fallbackCode: 'tg',
        sortOrder: 9,
    ));

    expect($updated->name)->toBe('Russian (RU)')
        ->and($updated->sort_order)->toBe(9);
    $this->assertDatabaseHas('locales', ['code' => 'ru', 'name' => 'Russian (RU)', 'sort_order' => 9]);
});

it('deletes a locale through the service', function () {
    $en = $this->locales->findOrFail('en');

    $this->service->deleteLocale($en);

    $this->assertDatabaseMissing('locales', ['code' => 'en']);
});

it('keeps exactly one default locale when a second default is created', function () {
    expect($this->locales->find('tg')?->is_default)->toBeTrue();

    $this->service->createLocale(new LocaleData(
        code: 'uz',
        name: 'Uzbek',
        nativeName: 'Oʻzbek',
        isDefault: true,
    ));

    expect($this->locales->find('uz')?->is_default)->toBeTrue()
        ->and($this->locales->find('tg')?->is_default)->toBeFalse();
    expect(Locale::query()->where('is_default', true)->count())->toBe(1);
});

it('moves the default flag when an existing locale is updated to default', function () {
    $ru = $this->locales->findOrFail('ru');

    $this->service->updateLocale($ru, new LocaleData(
        code: 'ru',
        name: 'Russian',
        nativeName: 'Русский',
        isDefault: true,
        fallbackCode: 'tg',
    ));

    expect($this->locales->find('ru')?->is_default)->toBeTrue()
        ->and($this->locales->find('tg')?->is_default)->toBeFalse();
    expect(Locale::query()->where('is_default', true)->count())->toBe(1);
});

it('returns only active locales through the active scope', function () {
    Locale::factory()->inactive()->create(['code' => 'fr', 'sort_order' => 5]);

    $active = $this->locales->active();

    expect($active->pluck('code')->all())->toContain('tg', 'ru', 'en')
        ->and($active->pluck('code')->all())->not->toContain('fr');
});

it('orders locales by sort_order then code', function () {
    $codes = $this->locales->all()->pluck('code')->all();

    expect($codes)->toBe(['tg', 'ru', 'en']);
});

it('throws DuplicateLocaleException when creating an existing code', function () {
    $this->service->createLocale(new LocaleData(
        code: 'ru',
        name: 'Russian Dup',
        nativeName: 'Русский',
        fallbackCode: 'tg',
    ));
})->throws(DuplicateLocaleException::class);

it('throws LocaleNotFoundException when finding an unregistered code', function () {
    $this->locales->findOrFail('zz');
})->throws(LocaleNotFoundException::class, 'Locale [zz] is not registered.');

it('exposes the default locale code via the service', function () {
    expect($this->service->defaultLocale())->toBe('tg');
});

it('exposes active locale codes via the service', function () {
    expect($this->service->activeLocales())->toBe(['tg', 'ru', 'en']);
});
