<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);
        $subtitle = fake()->sentence();

        return [
            'key' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'icon' => fake()->randomElement(['Phone', 'Send', 'BookOpen', 'Bell']),
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'subtitle' => ['tg' => $subtitle, 'ru' => $subtitle, 'en' => $subtitle],
            'is_primary' => false,
            'tel' => null,
            'route_key' => fake()->randomElement(['report', 'safety', 'subscribe']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
