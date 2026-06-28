<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Localization;

use App\Modules\Localization\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Translation>
 */
final class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group' => fake()->word(),
            'key' => fake()->word(),
            'locale' => 'tg',
            'value' => fake()->sentence(),
        ];
    }

    public function forLocale(string $l): self
    {
        return $this->state(fn (): array => [
            'locale' => $l,
        ]);
    }
}
