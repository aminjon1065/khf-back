<?php

use App\Modules\Media\Contracts\StorageManagerInterface;
use App\Modules\Media\Contracts\UrlGeneratorInterface;
use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Exceptions\MediaStorageException;
use App\Modules\Media\Models\Media;
use Illuminate\Support\Facades\Storage;

it('resolves storage drivers and reports the default', function () {
    $manager = app(StorageManagerInterface::class);

    expect($manager->defaultDriver())->toBe('local')
        ->and($manager->driver()->name())->toBe('local')
        ->and($manager->driver('s3')->name())->toBe('s3')
        ->and($manager->driver('local')->supportsTemporaryUrl())->toBeFalse()
        ->and($manager->driver('s3')->supportsTemporaryUrl())->toBeTrue();
});

it('throws for an unknown storage driver', function () {
    app(StorageManagerInterface::class)->driver('ftp');
})->throws(MediaStorageException::class);

it('builds a public url for a public asset', function () {
    Storage::fake('public');
    $media = Media::factory()->create([
        'driver' => 'local',
        'disk' => 'public',
        'path' => 'media/x/a.jpg',
    ]);

    expect(app(UrlGeneratorInterface::class)->url($media))->toContain('a.jpg');
});

it('serves a private asset through a signed download route', function () {
    Storage::fake('local');
    $media = Media::factory()->private()->create([
        'driver' => 'local',
        'disk' => 'local',
        'path' => 'media/x/secret.pdf',
        'original_file_name' => 'secret.pdf',
    ]);
    Storage::disk('local')->put($media->path, 'confidential-bytes');

    $signed = app(UrlGeneratorInterface::class)->temporaryUrl($media, now()->addMinutes(5));

    expect($signed)->toContain('/media/download/'.$media->id)
        ->and($signed)->toContain('signature=');

    $this->get($signed)->assertOk();
    $this->get(route('media.download', $media))->assertForbidden();
});

it('private assets resolve their default url to a signed url', function () {
    Storage::fake('local');
    $media = Media::factory()->private()->create(['driver' => 'local', 'disk' => 'local']);

    expect(app(UrlGeneratorInterface::class)->url($media))->toContain('signature=');
});

it('performs a put/get/exists/size/delete round trip through a driver', function () {
    Storage::fake('public');
    $driver = app(StorageManagerInterface::class)->driver('local');

    $driver->put('public', 'x/a.txt', 'hello', MediaVisibility::Public);

    expect($driver->exists('public', 'x/a.txt'))->toBeTrue()
        ->and($driver->get('public', 'x/a.txt'))->toBe('hello')
        ->and($driver->size('public', 'x/a.txt'))->toBe(5);

    $driver->delete('public', 'x/a.txt');

    expect($driver->exists('public', 'x/a.txt'))->toBeFalse();
});
