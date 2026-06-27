<?php

use App\Core\Enums\FieldType;
use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    $this->admin = User::where('email', 'admin@khf.tj')->first();
});

it('rejects an unknown field type', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    $this->actingAs($this->admin)
        ->post(route('admin.schema.blueprints.fields.store', $blueprint), [
            'name' => 'Bad',
            'handle' => 'bad',
            'type' => 'not_a_real_type',
        ])
        ->assertSessionHasErrors('type');
});

it('accepts every supported field type', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    foreach (FieldType::cases() as $case) {
        $this->actingAs($this->admin)
            ->post(route('admin.schema.blueprints.fields.store', $blueprint), [
                'name' => 'Field '.$case->value,
                'handle' => 'f_'.$case->value,
                'type' => $case->value,
            ])
            ->assertSessionHasNoErrors();
    }

    expect($blueprint->fields()->count())->toBe(count(FieldType::cases()));
});

it('rejects a duplicate collection slug', function () {
    Collection::factory()->create(['slug' => 'news']);

    $this->actingAs($this->admin)
        ->post(route('admin.schema.collections.store'), [
            'name' => 'News',
            'slug' => 'news',
        ])
        ->assertSessionHasErrors('slug');
});

it('rejects an entry with an invalid status', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    $this->actingAs($this->admin)
        ->post(route('admin.content.collections.entries.store', $collection), [
            'blueprint_id' => $blueprint->id,
            'status' => 'bogus',
            'data' => ['global' => ['title' => 'X']],
        ])
        ->assertSessionHasErrors('status');
});

it('defaults is_translatable from the field type when the flag is omitted', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    // Text is translatable-by-default; omit the flag entirely.
    $this->actingAs($this->admin)
        ->post(route('admin.schema.blueprints.fields.store', $blueprint), [
            'name' => 'Heading', 'handle' => 'heading', 'type' => 'text',
        ])
        ->assertSessionHasNoErrors();

    expect($blueprint->fields()->where('handle', 'heading')->firstOrFail()->is_translatable)->toBeTrue();
});

it('rejects an entry whose blueprint belongs to another collection', function () {
    $collection = Collection::factory()->create();
    $foreignBlueprint = Blueprint::factory()->create(); // belongs to a different collection

    $this->actingAs($this->admin)
        ->post(route('admin.content.collections.entries.store', $collection), [
            'blueprint_id' => $foreignBlueprint->id,
            'status' => 'draft',
            'data' => ['global' => ['title' => 'X']],
        ])
        ->assertSessionHasErrors('blueprint_id');
});

it('enforces unique field handles within a blueprint', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->for($collection)->create();

    $this->actingAs($this->admin)
        ->post(route('admin.schema.blueprints.fields.store', $blueprint), [
            'name' => 'Title', 'handle' => 'title', 'type' => 'text',
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($this->admin)
        ->post(route('admin.schema.blueprints.fields.store', $blueprint), [
            'name' => 'Title Again', 'handle' => 'title', 'type' => 'text',
        ])
        ->assertSessionHasErrors('handle');
});
