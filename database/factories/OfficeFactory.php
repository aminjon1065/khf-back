<?php

namespace Database\Factories;

use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Office>
 */
class OfficeFactory extends Factory
{
    protected $model = Office::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $region = fake()->city();
        $address = fake()->address();
        $hours = 'Душанбе–Ҷумъа, 8:00–17:00';

        return [
            'region' => ['tg' => $region, 'ru' => $region, 'en' => $region],
            'address' => ['tg' => $address, 'ru' => $address, 'en' => $address],
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'hours' => ['tg' => $hours, 'ru' => $hours, 'en' => $hours],
            'is_head' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function head(): static
    {
        return $this->state(fn (): array => [
            'is_head' => true,
        ]);
    }
}
