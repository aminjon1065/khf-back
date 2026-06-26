<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel' => fake()->randomElement(['email', 'sms', 'telegram']),
            'region' => fake()->randomElement(['Душанбе', 'Хатлон', 'Суғд', 'ВМКБ', 'НТҶ']),
            'categories' => fake()->randomElements(
                ['Пожар', 'Наводнение', 'Сель', 'Землетрясение', 'Погода'],
                fake()->numberBetween(1, 3),
            ),
            'contact' => fake()->safeEmail(),
            'status' => SubmissionStatus::New,
        ];
    }
}
