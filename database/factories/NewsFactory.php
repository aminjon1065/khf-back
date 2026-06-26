<?php

namespace Database\Factories;

use App\Enums\PublishStatus;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'news_category_id' => NewsCategory::factory(),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'excerpt' => [
                'tg' => fake()->sentence(),
                'ru' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'body' => [
                'tg' => '<p>'.fake()->paragraph().'</p>',
                'ru' => '<p>'.fake()->paragraph().'</p>',
                'en' => '<p>'.fake()->paragraph().'</p>',
            ],
            'author' => 'Пресс-центр КҲФ',
            'region' => fake()->randomElement(['Душанбе', 'Хатлон', 'Суғд', 'ВМКБ', 'НТҶ']),
            'views' => fake()->numberBetween(100, 5000),
            'status' => PublishStatus::Published,
            'published_at' => fake()->dateTimeBetween('-3 months'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => PublishStatus::Draft,
            'published_at' => null,
        ]);
    }
}
