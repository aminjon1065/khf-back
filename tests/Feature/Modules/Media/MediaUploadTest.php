<?php

use App\Models\User;
use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Enums\ImageFit;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Events\MediaConverted;
use App\Modules\Media\Events\MediaUploaded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

it('stores the original, extracts intrinsic metadata and a checksum', function () {
    $media = app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('photo.jpg', 640, 480),
        visibility: MediaVisibility::Public,
        conversions: [],
    ));

    expect($media->disk)->toBe('public')
        ->and($media->driver)->toBe('local')
        ->and($media->mime_type)->toBe('image/jpeg')
        ->and($media->width)->toBe(640)
        ->and($media->height)->toBe(480)
        ->and($media->file_name)->toBe('photo.jpg')
        ->and($media->checksum)->toHaveLength(64)
        ->and($media->dominant_color)->not->toBeNull()
        ->and($media->visibility)->toBe(MediaVisibility::Public);

    Storage::disk('public')->assertExists($media->path);
});

it('dispatches MediaUploaded after a successful upload', function () {
    Event::fake([MediaUploaded::class]);

    app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 100, 100),
        conversions: [],
    ));

    Event::assertDispatched(MediaUploaded::class);
});

it('generates the requested conversions during upload', function () {
    Event::fake([MediaConverted::class]);

    $media = app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 400, 400),
        conversions: [new ImageTransformation('thumbnail', 200, 200, ImageFit::Crop, 'webp')],
    ));

    $media->load('conversions');
    $conversion = $media->conversions->firstOrFail();

    expect($media->conversions)->toHaveCount(1)
        ->and($conversion->conversion_name)->toBe('thumbnail')
        ->and($conversion->format)->toBe('webp')
        ->and($conversion->mime_type)->toBe('image/webp')
        ->and($conversion->width)->toBe(200)
        ->and($conversion->height)->toBe(200);

    Storage::disk('public')->assertExists($conversion->path);
    Event::assertDispatched(MediaConverted::class);
});

it('records the uploader and persists supplied descriptive metadata', function () {
    $uploader = User::factory()->create();

    $media = app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 100, 100),
        altText: 'A descriptive alt',
        caption: 'A caption',
        uploadedBy: $uploader->id,
        conversions: [],
    ));

    expect($media->alt_text)->toBe('A descriptive alt')
        ->and($media->caption)->toBe('A caption')
        ->and($media->uploaded_by)->toBe($uploader->id)
        ->and($media->uploader->is($uploader))->toBeTrue();
});
