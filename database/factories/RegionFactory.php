<?php

namespace Database\Factories;

use App\Enums\RiskLevel;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->city();
        $center = fake()->city();
        $note = fake()->sentence();

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => ['tg' => $name, 'ru' => $name, 'en' => $name],
            'center' => ['tg' => $center, 'ru' => $center, 'en' => $center],
            'risk' => fake()->randomElement(RiskLevel::cases()),
            'active_incidents' => fake()->numberBetween(0, 10),
            'stations' => fake()->numberBetween(1, 20),
            'note' => ['tg' => $note, 'ru' => $note, 'en' => $note],
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
