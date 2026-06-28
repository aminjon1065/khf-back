<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\Translation;

beforeEach(function () {
    Locale::factory()->default()->create(['alias' => 'tj', 'sort_order' => 1]);
    Locale::factory()->create(['code' => 'ru', 'fallback_code' => 'tg', 'sort_order' => 2]);
    Locale::factory()->create(['code' => 'en', 'fallback_code' => 'tg', 'sort_order' => 3]);

    $this->service = app(LocalizationServiceInterface::class);
});

it('creates a translation row on first write', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'Захира кардан');

    $this->assertDatabaseHas('translations', [
        'group' => 'ui',
        'key' => 'save',
        'locale' => 'tg',
        'value' => 'Захира кардан',
    ]);
});

it('updates the value on a subsequent write to the same key', function () {
    $this->service->setTranslation('ui', 'save', 'ru', 'Сохранить');
    $this->service->setTranslation('ui', 'save', 'ru', 'Сохранять');

    expect($this->service->translate('ui', 'save', 'ru'))->toBe('Сохранять');
    expect(Translation::query()
        ->where('group', 'ui')->where('key', 'save')->where('locale', 'ru')->count())->toBe(1);
});

it('translates a key per locale', function () {
    $this->service->setTranslation('ui', 'hello', 'tg', 'Салом');
    $this->service->setTranslation('ui', 'hello', 'ru', 'Привет');
    $this->service->setTranslation('ui', 'hello', 'en', 'Hello');

    expect($this->service->translate('ui', 'hello', 'tg'))->toBe('Салом')
        ->and($this->service->translate('ui', 'hello', 'ru'))->toBe('Привет')
        ->and($this->service->translate('ui', 'hello', 'en'))->toBe('Hello');
});

it('forgets a translation and removes its row', function () {
    $this->service->setTranslation('ui', 'remove', 'tg', 'Нест кардан');
    expect($this->service->translate('ui', 'remove', 'tg'))->toBe('Нест кардан');

    $this->service->forgetTranslation('ui', 'remove', 'tg');

    $this->assertDatabaseMissing('translations', [
        'group' => 'ui',
        'key' => 'remove',
        'locale' => 'tg',
    ]);
    expect($this->service->translate('ui', 'remove', 'tg'))->toBeNull();
});

it('returns the full translation map for a locale keyed by group.key', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'Захира');
    $this->service->setTranslation('seo', 'title', 'tg', 'Сарлавҳа');

    $map = $this->service->translations('tg');

    expect($map)->toMatchArray([
        'ui.save' => 'Захира',
        'seo.title' => 'Сарлавҳа',
    ]);
});

it('returns a group-scoped map with the group prefix stripped', function () {
    $this->service->setTranslation('ui', 'save', 'tg', 'Захира');
    $this->service->setTranslation('ui', 'cancel', 'tg', 'Бекор кардан');
    $this->service->setTranslation('seo', 'title', 'tg', 'Сарлавҳа');

    $map = $this->service->translations('tg', 'ui');

    expect($map)->toBe([
        'save' => 'Захира',
        'cancel' => 'Бекор кардан',
    ]);
});

it('stores a null value without losing the row', function () {
    $this->service->setTranslation('ui', 'blank', 'tg', null);

    $this->assertDatabaseHas('translations', [
        'group' => 'ui',
        'key' => 'blank',
        'locale' => 'tg',
        'value' => null,
    ]);
});
