<?php

namespace Database\Factories;

use App\Enums\Tone;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NewsCategory>
 */
class NewsCategoryFactory extends Factory
{
    protected $model = NewsCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $word = fake()->unique()->word();

        return [
            'slug' => Str::slug($word).'-'.fake()->unique()->numberBetween(1, 9999),
            'name' => ['tg' => $word, 'ru' => $word, 'en' => $word],
            'tone' => fake()->randomElement(Tone::cases()),
            'sort_order' => 0,
        ];
    }
}
