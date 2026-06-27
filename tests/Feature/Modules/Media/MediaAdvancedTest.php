<?php

use App\Core\Contracts\HookManagerInterface;
use App\Modules\Media\Contracts\ImageProcessorInterface;
use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\DTOs\ImageTransformation;
use App\Modules\Media\DTOs\MediaData;
use App\Modules\Media\DTOs\ProcessedImage;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Enums\ImageFit;
use App\Modules\Media\Events\MediaConverted;
use App\Modules\Media\Events\MediaOptimized;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Support\MediaHooks;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

it('rotates a conversion by 90 degrees, swapping width and height', function () {
    Storage::fake('public');

    $service = app(MediaServiceInterface::class);
    $media = $service->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('wide.jpg', 400, 200),
        conversions: [],
    ));

    $conversion = $service->generateConversion(
        $media,
        new ImageTransformation(name: 'rotated', format: 'webp', rotation: 90),
    );

    expect($conversion->width)->toBe(200)
        ->and($conversion->height)->toBe(400);
});

it('dispatches MediaOptimized when optimization shrinks the original', function () {
    Storage::fake('public');
    Event::fake([MediaOptimized::class]);

    app()->bind(ImageProcessorInterface::class, fn (): ImageProcessorInterface => new class implements ImageProcessorInterface
    {
        public function isSupported(string $mimeType): bool
        {
            return str_starts_with($mimeType, 'image/');
        }

        public function convert(string $sourceAbsolutePath, ImageTransformation $transformation, string $destinationAbsolutePath): ProcessedImage
        {
            return new ProcessedImage($destinationAbsolutePath, 'webp', 'image/webp', 1, 1, 1);
        }

        public function optimize(string $absolutePath): int
        {
            return 1; // pretend optimization shrank the file to 1 byte
        }
    });

    app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 100, 100),
        conversions: [],
    ));

    Event::assertDispatched(MediaOptimized::class, fn (MediaOptimized $event): bool => $event->optimizedSize < $event->originalSize);
});

it('generates default and responsive conversions when none are specified', function () {
    Storage::fake('public');
    Event::fake([MediaConverted::class]);
    config([
        'khf.media.conversions' => [
            ['name' => 'thumbnail', 'width' => 100, 'height' => 100, 'fit' => 'crop', 'format' => 'webp', 'quality' => 80],
        ],
        'khf.media.responsive_widths' => [50],
    ]);

    $media = app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 300, 300),
        conversions: null, // null = use engine defaults
    ));

    $media->load('conversions');
    $names = $media->conversions->pluck('conversion_name')->all();

    expect($names)->toContain('thumbnail')
        ->and($names)->toContain('responsive-50')
        ->and($media->conversions->firstWhere('conversion_name', 'responsive-50')->format)->toBe('webp');
    Event::assertDispatched(MediaConverted::class);
});

it('lets a plugin register an extra conversion via the FILTER_CONVERSIONS hook', function () {
    Storage::fake('public');

    app(HookManagerInterface::class)->addFilter(
        MediaHooks::FILTER_CONVERSIONS,
        function (array $transformations): array {
            $transformations[] = new ImageTransformation('plugin-extra', 64, 64, ImageFit::Crop, 'webp');

            return $transformations;
        },
    );

    $media = app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('a.jpg', 200, 200),
        conversions: [], // base set is empty; the hook still contributes one
    ));

    $media->load('conversions');

    expect($media->conversions->pluck('conversion_name')->all())->toContain('plugin-extra');
});

it('builds a framework-agnostic MediaData DTO via toData()', function () {
    Storage::fake('public');

    $media = Media::factory()->create(['mime_type' => 'image/png', 'disk' => 'public', 'alt_text' => 'alt']);

    $data = app(MediaServiceInterface::class)->toData($media);

    expect($data)->toBeInstanceOf(MediaData::class)
        ->and($data->id)->toBe($media->id)
        ->and($data->type)->toBe('image')
        ->and($data->altText)->toBe('alt')
        ->and($data->url)->not->toBe('');
});
