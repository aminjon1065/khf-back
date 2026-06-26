<?php

namespace Database\Factories;

use App\Models\Slide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slide>
 */
class SlideFactory extends Factory
{
    protected $model = Slide::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->word();
        $title = fake()->sentence();

        return [
            'news_id' => null,
            'category' => ['tg' => $category, 'ru' => $category, 'en' => $category],
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'date' => fake()->dateTimeBetween('-3 months')->format('d.m.Y'),
            'source' => 'Пресс-центр КҲФ',
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
