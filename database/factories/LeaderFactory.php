<?php

namespace Database\Factories;

use App\Models\Leader;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Leader>
 */
class LeaderFactory extends Factory
{
    protected $model = Leader::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        $role = fake()->jobTitle();
        $rank = fake()->randomElement(['генерал-лейтенант', 'генерал-майор', 'полковник']);
        $bio = fake()->sentence();

        return [
            'name' => ['tg' => $name, 'ru' => $name, 'en' => $name],
            'role' => ['tg' => $role, 'ru' => $role, 'en' => $role],
            'rank' => ['tg' => $rank, 'ru' => $rank, 'en' => $rank],
            'bio' => ['tg' => $bio, 'ru' => $bio, 'en' => $bio],
            'sort_order' => 0,
        ];
    }
}
