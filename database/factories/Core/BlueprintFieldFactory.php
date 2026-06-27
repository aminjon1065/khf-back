<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Core\Enums\FieldType;
use App\Core\Models\Blueprint;
use App\Core\Models\BlueprintField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlueprintField>
 */
final class BlueprintFieldFactory extends Factory
{
    protected $model = BlueprintField::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->word().' '.fake()->word());

        return [
            'blueprint_id' => Blueprint::factory(),
            'name' => $name,
            'handle' => Str::snake($name),
            'type' => FieldType::Text,
            'is_translatable' => false,
            'validation_rules' => ['string', 'max:255'],
            'settings' => [],
            'order' => 0,
        ];
    }

    public function ofType(FieldType $type): static
    {
        return $this->state(fn (): array => ['type' => $type]);
    }

    public function translatable(): static
    {
        return $this->state(fn (): array => ['is_translatable' => true]);
    }
}
