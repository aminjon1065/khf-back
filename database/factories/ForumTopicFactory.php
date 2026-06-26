<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ForumTopic>
 */
class ForumTopicFactory extends Factory
{
    protected $model = ForumTopic::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'forum_category_id' => ForumCategory::factory(),
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'author' => fake()->firstName(),
            'replies' => fake()->numberBetween(0, 100),
            'views' => fake()->numberBetween(0, 5000),
            'pinned' => fake()->boolean(20),
            'last_activity' => fake()->randomElement(['2 соат пеш', 'дирӯз', '2 рӯз пеш']),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (): array => ['pinned' => true]);
    }
}
