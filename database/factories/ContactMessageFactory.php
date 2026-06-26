<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->optional()->sentence(),
            'message' => fake()->paragraph(),
            'status' => SubmissionStatus::New,
        ];
    }
}
