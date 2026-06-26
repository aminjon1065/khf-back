<?php

namespace Database\Factories;

use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentCategory>
 */
class DocumentCategoryFactory extends Factory
{
    protected $model = DocumentCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'name' => ['tg' => $name, 'ru' => $name, 'en' => $name],
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
