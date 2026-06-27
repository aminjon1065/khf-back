<?php

use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::where('email', 'admin@khf.tj')->first();
    $this->editor = User::where('email', 'editor@khf.tj')->first();
});

it('allows admin with manage schema permission to view collections', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.schema.collections.index'))
        ->assertOk();
});

it('denies editor without manage schema permission to view collections', function () {
    $this->actingAs($this->editor)
        ->get(route('admin.schema.collections.index'))
        ->assertForbidden();
});

it('allows admin to create a new collection', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.schema.collections.store'), [
            'name' => 'Articles',
            'slug' => 'articles',
        ])
        ->assertRedirect();

    expect(Collection::where('slug', 'articles')->exists())->toBeTrue();
});

it('allows admin to create a blueprint under a collection', function () {
    $collection = Collection::create(['name' => 'Test', 'slug' => 'test']);

    $this->actingAs($this->admin)
        ->post(route('admin.schema.collections.blueprints.store', $collection), [
            'name' => 'Default Blueprint',
        ])
        ->assertRedirect();

    expect(Blueprint::where('collection_id', $collection->id)->exists())->toBeTrue();
});
