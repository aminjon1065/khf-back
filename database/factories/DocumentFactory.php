<?php

namespace Database\Factories;

use App\Enums\DocType;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'document_category_id' => DocumentCategory::factory(),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'title' => ['tg' => $title, 'ru' => $title, 'en' => $title],
            'number' => '№ '.fake()->numberBetween(1, 999),
            'document_date' => fake()->dateTimeBetween('-5 years'),
            'type' => fake()->randomElement(DocType::cases()),
            'size' => fake()->numberBetween(50, 999).' КБ',
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
