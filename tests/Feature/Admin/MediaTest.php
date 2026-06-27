<?php

use App\Core\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = clone User::factory()->create();

    // Assign admin role to pass EnsureCanAccessAdmin
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->user->assignRole($role);

    // Give specific permission
    $permission = Permission::firstOrCreate(['name' => 'manage media']);
    $this->user->givePermissionTo($permission);

    Storage::fake('public');
});

it('allows authorized users to view media list', function () {
    $this->withoutVite();
    $response = $this->actingAs($this->user)->get('/admin/media');
    $response->assertStatus(200);
});

it('allows authorized users to upload a file', function () {
    $file = UploadedFile::fake()->image('avatar.jpg')->size(100); // 100kb

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

    $media = Media::first();
    Storage::disk('public')->assertExists($media->path);
});

it('allows authorized users to delete a file', function () {
    $file = UploadedFile::fake()->image('test.jpg');
    $path = $file->store('media', 'public');

    $media = Media::create([
        'file_name' => 'test.jpg',
        'path' => $path,
        'disk' => 'public',
        'mime_type' => 'image/jpeg',
        'size' => 1234,
    ]);

    Storage::disk('public')->assertExists($path);

    $response = $this->actingAs($this->user)->delete("/admin/media/{$media->id}");

    $response->assertRedirect(); // back()

    $this->assertSoftDeleted('media', ['id' => $media->id]);

    // In our implementation, we also delete the physical file.
    Storage::disk('public')->assertMissing($path);
});
