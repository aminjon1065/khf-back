<?php

use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\Contracts\StorageManagerInterface;
use App\Modules\Media\Contracts\UrlGeneratorInterface;
use App\Modules\Media\DTOs\UploadMediaData;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Exceptions\DangerousFileException;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use App\Modules\Media\Storage\AbstractStorageDriver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('rejects HTML content disguised with an image name (stored-XSS guard)', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->createWithContent('photo.jpg', '<!DOCTYPE html><script>alert(1)</script>');

    app(MediaServiceInterface::class)->upload(new UploadMediaData(file: $file, conversions: []));
})->throws(DangerousFileException::class);

it('rejects SVG content disguised as a png (stored-XSS guard)', function () {
    Storage::fake('public');

    $svg = '<?xml version="1.0"?><svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';
    $file = UploadedFile::fake()->createWithContent('logo.png', $svg);

    app(MediaServiceInterface::class)->upload(new UploadMediaData(file: $file, conversions: []));
})->throws(DangerousFileException::class);

it('stores a private upload on the non-web private disk, not the public disk', function () {
    Storage::fake('public');
    Storage::fake('local');

    $media = app(MediaServiceInterface::class)->upload(new UploadMediaData(
        file: UploadedFile::fake()->image('secret.jpg', 60, 60),
        visibility: MediaVisibility::Private,
        conversions: [],
    ));

    expect($media->disk)->toBe('local')
        ->and($media->visibility)->toBe(MediaVisibility::Private);
    Storage::disk('local')->assertExists($media->path);
    Storage::disk('public')->assertMissing($media->path);
});

it('returns a signed url for a private conversion instead of a public one', function () {
    Storage::fake('local');

    $media = Media::factory()->private()->create(['disk' => 'local', 'driver' => 'local']);
    $conversion = MediaConversion::factory()->for($media)->create([
        'visibility' => MediaVisibility::Private,
        'disk' => 'local',
        'driver' => 'local',
    ]);

    $url = app(UrlGeneratorInterface::class)->conversionUrl($conversion);

    expect($url)->toContain('/media/conversion/'.$conversion->id)
        ->and($url)->toContain('signature=');
});

it('allows registering additional storage drivers (pluggability)', function () {
    $manager = app(StorageManagerInterface::class);

    $manager->register(new class extends AbstractStorageDriver
    {
        public function name(): string
        {
            return 'memory';
        }

        public function supportsTemporaryUrl(): bool
        {
            return false;
        }

        public function temporaryUrl(string $disk, string $path, DateTimeInterface $expiresAt): string
        {
            return '';
        }
    });

    expect($manager->driver('memory')->name())->toBe('memory');
});
