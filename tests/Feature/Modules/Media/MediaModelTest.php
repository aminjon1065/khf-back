<?php

use App\Modules\Media\Enums\MediaType;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;

it('classifies its media type from the MIME type', function () {
    expect(Media::factory()->create(['mime_type' => 'image/png'])->type())->toBe(MediaType::Image)
        ->and(Media::factory()->create(['mime_type' => 'video/mp4'])->type())->toBe(MediaType::Video)
        ->and(Media::factory()->document()->create()->type())->toBe(MediaType::Document)
        ->and(Media::factory()->create(['mime_type' => 'application/zip'])->type())->toBe(MediaType::Other);
});

it('casts visibility, json columns and integers', function () {
    $media = Media::factory()->create([
        'visibility' => MediaVisibility::Private,
        'focal_point' => ['x' => 0.1, 'y' => 0.2],
        'exif' => ['Make' => 'Canon'],
    ]);

    expect($media->visibility)->toBe(MediaVisibility::Private)
        ->and($media->focal_point)->toBe(['x' => 0.1, 'y' => 0.2])
        ->and($media->exif)->toBe(['Make' => 'Canon'])
        ->and($media->width)->toBeInt();
});

it('relates conversions to their media', function () {
    $media = Media::factory()->create();
    MediaConversion::factory()->for($media)->count(2)->create();

    expect($media->conversions()->count())->toBe(2)
        ->and($media->conversions->first()->media->is($media))->toBeTrue();
});

it('soft deletes and uses a uuid primary key', function () {
    $media = Media::factory()->create();

    expect(strlen($media->id))->toBe(36);

    $media->delete();
    $this->assertSoftDeleted($media);
});
