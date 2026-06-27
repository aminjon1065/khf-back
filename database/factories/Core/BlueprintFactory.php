<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Core\Models\Blueprint;
use App\Core\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blueprint>
 */
final class BlueprintFactory extends Factory
{
    protected $model = Blueprint::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'name' => Str::title(fake()->word().' '.fake()->word()).' Blueprint',
        ];
    }
}
