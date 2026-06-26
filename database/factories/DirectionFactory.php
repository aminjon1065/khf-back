<?php

namespace Database\Factories;

use App\Models\Direction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Direction>
 */
class DirectionFactory extends Factory
{
    protected $model = Direction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);
        $label = fake()->words(2, true);

        return [
            'key' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'icon' => fake()->randomElement(['LifeBuoy', 'ShieldAlert', 'Users', 'Flame', 'CloudRain', 'GraduationCap']),
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'description' => [
                'tg' => fake()->sentence(),
                'ru' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'stat_value' => (string) fake()->numberBetween(10, 50000),
            'stat_label' => ['tg' => $label, 'ru' => $label, 'en' => $label],
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
