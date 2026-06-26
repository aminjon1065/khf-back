<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);
        $description = fake()->sentence();
        $head = fake()->randomElement(['управление', 'служба', 'центр']);

        return [
            'icon' => fake()->randomElement(['Building2', 'Shield', 'Radio']),
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'description' => ['tg' => $description, 'ru' => $description, 'en' => $description],
            'head' => ['tg' => $head, 'ru' => $head, 'en' => $head],
            'sort_order' => 0,
        ];
    }
}
