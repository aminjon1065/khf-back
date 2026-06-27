<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Identity;

use App\Modules\Identity\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
final class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'type' => fake()->randomElement(['auth.login', 'user.created', 'role.assigned', 'permission.granted']),
            'description' => fake()->sentence(),
            'subject_type' => null,
            'subject_id' => null,
            'properties' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'created_at' => now(),
        ];
    }
}
