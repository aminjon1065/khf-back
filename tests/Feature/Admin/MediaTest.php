<?php

use App\Models\User;
use App\Modules\Media\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::factory()->create();

    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->user->assignRole($role);
    $permission = Permission::firstOrCreate(['name' => 'manage media']);
    $this->user->givePermissionTo($permission);

    Storage::fake('public');

    // Keep the admin HTTP tests focused and fast: no auto-conversions here.
    config(['khf.media.conversions' => [], 'khf.media.responsive_widths' => []]);
});

it('allows authorized users to view media list', function () {
    $this->withoutVite();

    $this->actingAs($this->user)
        ->get('/admin/media')
        ->assertStatus(200);
});

it('allows authorized users to upload a file', function () {
    $file = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(100);

    $response = $this->actingAs($this->user)->post('/admin/media', [
        'file' => $file,
    ], ['Accept' => 'application/json']);

    $response->assertStatus(200)
        ->assertJsonStructure(['id', 'file_name', 'url']);

    $this->assertDatabaseHas('media', [
        'file_name' => 'avatar.jpg',
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
    ]);

    $media = Media::firstOrFail();
    expect($media->width)->toBe(200)
        ->and($media->height)->toBe(200)
        ->and($media->checksum)->not->toBeNull();
    Storage::disk('public')->assertExists($media->path);
});

it('soft deletes a file through the admin endpoint and keeps it restorable', function () {
    $file = UploadedFile::fake()->image('test.jpg', 100, 100);
    config(['filesystems.default' => 'public']);

    $media = $this->actingAs($this->user)->post('/admin/media', ['file' => $file], ['Accept' => 'application/json']);
    $created = Media::firstOrFail();
    Storage::disk('public')->assertExists($created->path);

    $this->actingAs($this->user)
        ->delete("/admin/media/{$created->id}")
        ->assertRedirect();

    $this->assertSoftDeleted('media', ['id' => $created->id]);

    // Soft delete is a trash operation: the stored file survives for restore.
    Storage::disk('public')->assertExists($created->path);
});

it('forbids users without the manage media permission', function () {
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->get('/admin/media')
        ->assertForbidden();
});
