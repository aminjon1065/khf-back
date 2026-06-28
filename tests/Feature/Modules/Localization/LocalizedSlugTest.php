<?php

declare(strict_types=1);

use App\Modules\Localization\Contracts\LocalizationServiceInterface;
use App\Modules\Localization\Contracts\LocalizedSlugRepositoryInterface;
use App\Modules\Localization\Models\Locale;
use App\Modules\Localization\Models\LocalizedSlug;

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

it('persists a slug via setSlug and resolves it back per locale', function (): void {
    $this->localization->setSlug('entry', '42', 'tg', 'navqalho-khabar');
    $this->localization->setSlug('entry', '42', 'ru', 'novosti');

    expect($this->localization->localizedSlug('entry', '42', 'tg'))->toBe('navqalho-khabar');
    expect($this->localization->localizedSlug('entry', '42', 'ru'))->toBe('novosti');
});

it('stores the slug row with the expected attributes', function (): void {
    $row = $this->localization->setSlug('entry', '7', 'ru', 'pomoshch', true);

    expect($row)->toBeInstanceOf(LocalizedSlug::class)
        ->and($row->subject_type)->toBe('entry')
        ->and($row->subject_id)->toBe('7')
        ->and($row->locale)->toBe('ru')
        ->and($row->slug)->toBe('pomoshch')
        ->and($row->is_canonical)->toBeTrue();
});

it('updates the slug in place for the same subject and locale', function (): void {
    $this->localization->setSlug('entry', '9', 'tg', 'first');
    $this->localization->setSlug('entry', '9', 'tg', 'second');

    expect($this->localization->localizedSlug('entry', '9', 'tg'))->toBe('second')
        ->and(LocalizedSlug::query()->where('subject_id', '9')->where('locale', 'tg')->count())->toBe(1);
});

it('falls back through the chain to the parent locale slug when missing', function (): void {
    // Only a tg slug exists; resolving for ru must walk ru -> tg.
    $this->localization->setSlug('entry', '5', 'tg', 'maqola');

    expect($this->localization->localizedSlug('entry', '5', 'ru'))->toBe('maqola');
});

it('falls back to the canonical slug when no chain locale matches', function (): void {
    // A canonical slug stored under a locale outside the ru->tg chain.
    $this->localization->setSlug('entry', '11', 'en', 'english-only', true);

    expect($this->localization->localizedSlug('entry', '11', 'ru'))->toBe('english-only');
});

it('returns null when neither a chain slug nor a canonical slug exists', function (): void {
    expect($this->localization->localizedSlug('entry', 'nope', 'ru'))->toBeNull();
});

it('mints a unique slug appending -2 on collision', function (): void {
    LocalizedSlug::factory()->create([
        'subject_type' => 'entry', 'subject_id' => '1', 'locale' => 'tg', 'slug' => 'help',
    ]);

    expect($this->localization->uniqueSlug('entry', 'tg', 'Help'))->toBe('help-2');
});

it('keeps incrementing the suffix while candidates collide', function (): void {
    LocalizedSlug::factory()->create(['subject_type' => 'entry', 'subject_id' => '1', 'locale' => 'tg', 'slug' => 'help']);
    LocalizedSlug::factory()->create(['subject_type' => 'entry', 'subject_id' => '2', 'locale' => 'tg', 'slug' => 'help-2']);

    expect($this->localization->uniqueSlug('entry', 'tg', 'Help'))->toBe('help-3');
});

it('returns the bare slug when there is no collision', function (): void {
    expect($this->localization->uniqueSlug('entry', 'tg', 'Brand New Title'))->toBe('brand-new-title');
});

it('scopes slug uniqueness per locale', function (): void {
    $repo = app(LocalizedSlugRepositoryInterface::class);
    $this->localization->setSlug('entry', '3', 'tg', 'shared');

    expect($repo->slugExists('entry', 'tg', 'shared'))->toBeTrue()
        ->and($repo->slugExists('entry', 'ru', 'shared'))->toBeFalse();
});

it('ignores the given id when checking slug existence', function (): void {
    $repo = app(LocalizedSlugRepositoryInterface::class);
    $row = $this->localization->setSlug('entry', '8', 'tg', 'about');

    expect($repo->slugExists('entry', 'tg', 'about'))->toBeTrue()
        ->and($repo->slugExists('entry', 'tg', 'about', $row->id))->toBeFalse();
});
