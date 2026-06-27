<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Core\Enums\EntryStatus;
use App\Core\Models\Blueprint;
use App\Core\Models\Entry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entry>
 */
final class EntryFactory extends Factory
{
    protected $model = Entry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Derive both keys from one blueprint so the entry's collection always
        // matches its blueprint's collection in the default state.
        $blueprint = Blueprint::factory()->create();

        return [
            'collection_id' => $blueprint->collection_id,
            'blueprint_id' => $blueprint->id,
            'author_id' => null,
            'updated_by' => null,
            'status' => EntryStatus::Draft,
            'slug' => 'entry-'.fake()->unique()->numberBetween(1, 9999999),
            'data' => ['global' => ['title' => fake()->sentence()]],
            'version' => 1,
            'published_at' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => EntryStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => EntryStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => EntryStatus::Archived,
        ]);
    }
}
