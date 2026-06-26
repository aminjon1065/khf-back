<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ForumCategory>
 */
class ForumCategoryFactory extends Factory
{
    protected $model = ForumCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'icon' => 'MessagesSquare',
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'description' => [
                'tg' => fake()->sentence(),
                'ru' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'topics_count' => fake()->numberBetween(0, 300),
            'posts_count' => fake()->numberBetween(0, 2000),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
