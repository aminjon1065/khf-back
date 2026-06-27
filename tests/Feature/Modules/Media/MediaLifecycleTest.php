<?php

use App\Modules\Media\Contracts\MediaServiceInterface;
use App\Modules\Media\Events\MediaDeleted;
use App\Modules\Media\Events\MediaRestored;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

it('soft deletes without removing files and dispatches MediaDeleted', function () {
    Storage::fake('public');
    Event::fake([MediaDeleted::class]);

    $media = Media::factory()->create(['disk' => 'public', 'path' => 'media/x/a.jpg']);
    Storage::disk('public')->put($media->path, 'bytes');

    app(MediaServiceInterface::class)->delete($media);

    $this->assertSoftDeleted('media', ['id' => $media->id]);
    Storage::disk('public')->assertExists($media->path);
    Event::assertDispatched(MediaDeleted::class, fn (MediaDeleted $event): bool => $event->permanently === false);
});

it('permanently deletes and removes the original and conversion files', function () {
    Storage::fake('public');

    $media = Media::factory()->create(['disk' => 'public', 'path' => 'media/x/a.jpg']);
    $conversion = MediaConversion::factory()->for($media)->create([
        'disk' => 'public',
        'path' => 'media/x/conversions/thumb.webp',
    ]);
    Storage::disk('public')->put($media->path, 'original');
    Storage::disk('public')->put($conversion->path, 'derived');

    app(MediaServiceInterface::class)->delete($media, permanently: true);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
    $this->assertDatabaseMissing('media_conversions', ['id' => $conversion->id]);
    Storage::disk('public')->assertMissing($media->path);
    Storage::disk('public')->assertMissing($conversion->path);
});

it('restores a trashed asset and dispatches MediaRestored', function () {
    Event::fake([MediaRestored::class]);

    $media = Media::factory()->create();
    $media->delete();

    $restored = app(MediaServiceInterface::class)->restore($media);

    expect($restored->trashed())->toBeFalse();
    $this->assertDatabaseHas('media', ['id' => $media->id, 'deleted_at' => null]);
    Event::assertDispatched(MediaRestored::class);
});
