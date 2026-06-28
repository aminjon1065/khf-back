<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Localization;

use App\Modules\Localization\Models\LocalizedSlug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LocalizedSlug>
 */
final class LocalizedSlugFactory extends Factory
{
    protected $model = LocalizedSlug::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_type' => 'entry',
            'subject_id' => (string) fake()->numberBetween(1, 9999),
            'locale' => 'tg',
            'slug' => fake()->unique()->slug(),
            'is_canonical' => false,
        ];
    }

    public function canonical(): self
    {
        return $this->state(fn (): array => [
            'is_canonical' => true,
        ]);
    }
}
