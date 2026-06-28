<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Models\Locale;

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
    Locale::factory()->create([
        'code' => 'en', 'name' => 'English', 'native_name' => 'English',
        'is_default' => false, 'is_active' => true, 'fallback_code' => 'tg',
        'alias' => null, 'sort_order' => 3,
    ]);

    $this->localization = app(LocalizationServiceInterface::class);
});

it('prefixes the default locale with its public alias', function (): void {
    expect($this->localization->url('/news', 'tg'))->toBe('/tj/news');
});

it('prefixes a non-default locale with its code', function (): void {
    expect($this->localization->url('/news', 'ru'))->toBe('/ru/news')
        ->and($this->localization->url('/news', 'en'))->toBe('/en/news');
});

it('normalises a path without a leading slash', function (): void {
    expect($this->localization->url('news', 'ru'))->toBe('/ru/news');
});

it('produces just the segment for the root path', function (): void {
    expect($this->localization->url('/', 'ru'))->toBe('/ru')
        ->and($this->localization->url('/', 'tg'))->toBe('/tj');
});

it('defaults to the default locale when none is given', function (): void {
    expect($this->localization->url('/news'))->toBe('/tj/news');
});

it('returns a localized url for every active locale via urlsForPath', function (): void {
    expect($this->localization->urlsForPath('/news'))->toBe([
        'tg' => '/tj/news',
        'ru' => '/ru/news',
        'en' => '/en/news',
    ]);
});

it('returns a bare path for the default locale when prefixing is disabled', function (): void {
    config()->set('khf.localization.prefix_default_locale', false);

    expect($this->localization->url('/news', 'tg'))->toBe('/news')
        ->and($this->localization->url('/news', 'ru'))->toBe('/ru/news');
});

it('keeps the default locale prefixed by default', function (): void {
    config()->set('khf.localization.prefix_default_locale', true);

    expect($this->localization->url('/contact', 'tg'))->toBe('/tj/contact');
});
