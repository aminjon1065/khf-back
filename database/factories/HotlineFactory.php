<?php

namespace Database\Factories;

use App\Models\Hotline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hotline>
 */
class HotlineFactory extends Factory
{
    protected $model = Hotline::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = fake()->words(2, true);
        $note = fake()->sentence();

        return [
            'number' => fake()->phoneNumber(),
            'label' => ['tg' => $label, 'ru' => $label, 'en' => $label],
            'note' => ['tg' => $note, 'ru' => $note, 'en' => $note],
            'is_primary' => false,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (): array => [
            'is_primary' => true,
        ]);
    }
}
