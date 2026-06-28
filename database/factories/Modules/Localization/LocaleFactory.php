<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Localization;

use App\Modules\Localization\Enums\TextDirection;
use App\Modules\Localization\Models\Locale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Locale>
 */
final class LocaleFactory extends Factory
{
    protected $model = Locale::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('??'),
            'name' => fake()->word(),
            'native_name' => fake()->word(),
            'direction' => TextDirection::Ltr->value,
            'is_default' => false,
            'is_active' => true,
            'fallback_code' => null,
            'alias' => null,
            'sort_order' => 0,
        ];
    }

    public function default(): self
    {
        return $this->state(fn (): array => [
            'code' => 'tg',
            'is_default' => true,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    public function rtl(): self
    {
        return $this->state(fn (): array => [
            'direction' => TextDirection::Rtl->value,
        ]);
    }
}
