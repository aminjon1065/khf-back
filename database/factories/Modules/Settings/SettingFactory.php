<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Settings;

use App\Modules\Settings\Enums\SettingType;
use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
final class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $group = fake()->randomElement(['general', 'branding', 'seo']);
        $key = fake()->unique()->slug(2);

        return [
            'key' => "{$group}.{$key}",
            'group' => $group,
            'type' => SettingType::String->value,
            'value' => fake()->sentence(3),
        ];
    }

    /**
     * Persisted override without engine metadata (mirrors a legacy singleton row).
     */
    public function untyped(): self
    {
        return $this->state(fn (): array => [
            'group' => null,
            'type' => null,
        ]);
    }

    public function ofType(SettingType $type, mixed $value): self
    {
        return $this->state(fn (): array => [
            'type' => $type->value,
            'value' => $value,
        ]);
    }
}
