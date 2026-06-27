<?php

use App\Core\Enums\EntryStatus;

it('exposes draft, published and archived cases', function () {
    expect(EntryStatus::cases())->toHaveCount(3)
        ->and(EntryStatus::Draft->value)->toBe('draft')
        ->and(EntryStatus::Published->value)->toBe('published')
        ->and(EntryStatus::Archived->value)->toBe('archived');
});

it('provides a human label for each case', function () {
    expect(EntryStatus::Draft->label())->toBe('Draft')
        ->and(EntryStatus::Published->label())->toBe('Published')
        ->and(EntryStatus::Archived->label())->toBe('Archived');
});
