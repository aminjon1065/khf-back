<?php

namespace Database\Factories;

use App\Enums\ProgramStatus;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $start = fake()->numberBetween(2016, 2025);

        return [
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'description' => [
                'tg' => fake()->sentence(),
                'ru' => fake()->sentence(),
                'en' => fake()->sentence(),
            ],
            'period' => $start.'–'.($start + fake()->numberBetween(1, 10)),
            'status' => fake()->randomElement(ProgramStatus::cases()),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
