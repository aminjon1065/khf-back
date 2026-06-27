<?php

use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Enums\ImageFit;
use App\Modules\Media\Events\MediaConverted;
use App\Modules\Media\Exceptions\UnsupportedMediaTypeException;
use App\Modules\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

it('generates a WebP conversion from an existing asset', function () {
    Storage::fake('public');
    Event::fake([MediaConverted::class]);

    $service = app(MediaServiceInterface::class);
    $media = $service->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 500, 500),
        conversions: [],
    ));

    $conversion = $service->generateConversion(
        $media,
        new ImageTransformation('square', 250, 250, ImageFit::Crop, 'webp'),
    );

    expect($conversion->conversion_name)->toBe('square')
        ->and($conversion->format)->toBe('webp')
        ->and($conversion->mime_type)->toBe('image/webp')
        ->and($conversion->width)->toBe(250)
        ->and($conversion->height)->toBe(250)
        ->and($conversion->media_id)->toBe($media->id);

    Storage::disk('public')->assertExists($conversion->path);
    Event::assertDispatched(MediaConverted::class);
});

it('refuses to convert a non-image asset', function () {
    $media = Media::factory()->document()->create();

    app(MediaServiceInterface::class)->generateConversion(
        $media,
        new ImageTransformation('thumb', 100, 100),
    );
})->throws(UnsupportedMediaTypeException::class);
