<?php

use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\DTOs\UpdateMetadataData;
use App\Modules\Media\Events\MediaUpdated;
use App\Modules\Media\Models\Media;
use Illuminate\Support\Facades\Event;

it('updates editable metadata and dispatches MediaUpdated', function () {
    Event::fake([MediaUpdated::class]);

    $media = Media::factory()->create(['alt_text' => null, 'caption' => null]);

    $updated = app(MediaServiceInterface::class)->updateMetadata($media, new UpdateMetadataData(
        name: 'Hero image',
        altText: 'A cat on a mat',
        caption: 'Caption text',
        copyright: '© KHF',
        focalPoint: ['x' => 0.5, 'y' => 0.4],
        customProperties: ['credit' => 'Photographer'],
    ));

    expect($updated->name)->toBe('Hero image')
        ->and($updated->alt_text)->toBe('A cat on a mat')
        ->and($updated->caption)->toBe('Caption text')
        ->and($updated->copyright)->toBe('© KHF')
        ->and($updated->focal_point)->toBe(['x' => 0.5, 'y' => 0.4])
        ->and($updated->custom_properties)->toBe(['credit' => 'Photographer']);

    Event::assertDispatched(MediaUpdated::class);
});

it('leaves unspecified metadata untouched', function () {
    $media = Media::factory()->create(['alt_text' => 'keep me', 'caption' => 'original']);

    $updated = app(MediaServiceInterface::class)->updateMetadata($media, new UpdateMetadataData(
        caption: 'changed',
    ));

    expect($updated->alt_text)->toBe('keep me')
        ->and($updated->caption)->toBe('changed');
});
