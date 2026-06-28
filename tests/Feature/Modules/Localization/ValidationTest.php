<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\DTOs\LocaleData;
use App\Modules\Localization\Enums\TextDirection;
use App\Modules\Localization\Exceptions\DuplicateLocaleException;
use App\Modules\Localization\Exceptions\LocalizationException;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Services\LocalizationValidator;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    Locale::factory()->create([
        'code' => 'tg', 'name' => 'Tajik', 'native_name' => 'Тоҷикӣ',
        'is_default' => true, 'is_active' => true, 'fallback_code' => null,
        'alias' => 'tj', 'sort_order' => 1,
    ]);
    Locale::factory()->create([
        'code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский',
        'is_default' => false, 'is_active' => true, 'fallback_code' => 'tg',
        'alias' => null, 'sort_order' => 2,
    ]);

    $this->localization = app(LocalizationServiceInterface::class);
});

it('rejects a malformed locale code when creating a locale', function (): void {
    $this->localization->createLocale(new LocaleData(
        code: 'TOOLONG',
        name: 'Bad',
        nativeName: 'Bad',
    ));
})->throws(ValidationException::class);

it('rejects an uppercase locale code', function (): void {
    $this->localization->createLocale(new LocaleData(
        code: 'EN',
        name: 'English',
        nativeName: 'English',
    ));
})->throws(ValidationException::class);

it('rejects a blank name', function (): void {
    app(LocalizationValidator::class)->validateLocale(new LocaleData(
        code: 'de',
        name: '',
        nativeName: 'Deutsch',
    ));
})->throws(ValidationException::class);

it('accepts a region-suffixed code such as en-US', function (): void {
    $locale = $this->localization->createLocale(new LocaleData(
        code: 'en-US',
        name: 'American English',
        nativeName: 'English (US)',
    ));

    expect($locale->code)->toBe('en-US');
});

it('rejects an unknown direction string at the DTO boundary', function (): void {
    LocaleData::fromArray([
        'code' => 'de',
        'name' => 'German',
        'native_name' => 'Deutsch',
        'direction' => 'sideways',
    ]);
})->throws(ValueError::class);

it('accepts both ltr and rtl directions through the validator', function (): void {
    $validator = app(LocalizationValidator::class);

    $validator->validateLocale(new LocaleData(code: 'ar', name: 'Arabic', nativeName: 'العربية', direction: TextDirection::Rtl));
    $validator->validateLocale(new LocaleData(code: 'de', name: 'German', nativeName: 'Deutsch', direction: TextDirection::Ltr));
})->throwsNoExceptions();

it('throws DuplicateLocaleException when the code already exists', function (): void {
    $this->localization->createLocale(new LocaleData(
        code: 'ru',
        name: 'Russian',
        nativeName: 'Русский',
    ));
})->throws(DuplicateLocaleException::class);

it('rejects a fallback that points at a non-existent locale', function (): void {
    $this->localization->createLocale(new LocaleData(
        code: 'de',
        name: 'German',
        nativeName: 'Deutsch',
        fallbackCode: 'zz',
    ));
})->throws(LocalizationException::class);

it('accepts a fallback that points at an existing locale', function (): void {
    $locale = $this->localization->createLocale(new LocaleData(
        code: 'de',
        name: 'German',
        nativeName: 'Deutsch',
        fallbackCode: 'tg',
    ));

    expect($locale->fallback_code)->toBe('tg');
});

it('rejects a translation targeting an unsupported locale', function (): void {
    $this->localization->setTranslation('ui', 'save', 'zz', 'Сохранить');
})->throws(ValidationException::class);

it('accepts a translation targeting a supported locale', function (): void {
    $this->localization->setTranslation('ui', 'save', 'ru', 'Сохранить');

    expect($this->localization->translate('ui', 'save', 'ru'))->toBe('Сохранить');
});

it('rejects a translation with a blank group', function (): void {
    app(LocalizationValidator::class)->validateTranslation('', 'save', 'ru', 'x');
})->throws(ValidationException::class);
