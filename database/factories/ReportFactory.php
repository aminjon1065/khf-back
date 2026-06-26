<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'ЧС-'.date('Y').'-'.Str::upper(Str::random(6)),
            'type' => fake()->randomElement(['Пожар', 'Наводнение', 'Сель', 'Землетрясение', 'ДТП']),
            'region' => fake()->randomElement(['Душанбе', 'Хатлон', 'Суғд', 'ВМКБ', 'НТҶ']),
            'location' => fake()->streetAddress(),
            'description' => fake()->paragraph(),
            'phone' => '+992'.fake()->numerify('#########'),
            'status' => SubmissionStatus::New,
        ];
    }
}
