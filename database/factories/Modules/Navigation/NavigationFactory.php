<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Navigation;

use App\Modules\Navigation\Enums\NavigationType;
use App\Modules\Navigation\Models\Navigation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Navigation>
 */
final class NavigationFactory extends Factory
{
    protected $model = Navigation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'handle' => fake()->unique()->slug(2),
            'name' => fake()->words(2, true),
            'type' => NavigationType::Header,
            'description' => null,
            'is_active' => true,
            'settings' => null,
        ];
    }

    public function ofType(NavigationType $type): self
    {
        return $this->state(fn (): array => ['type' => $type]);
    }

    public function inactive(): self
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
