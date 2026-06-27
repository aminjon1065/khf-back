<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Media;

use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Models\MediaConversion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaConversion>
 */
final class MediaConversionFactory extends Factory
{
    protected $model = MediaConversion::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Unique per (media_id, conversion_name) — the table enforces it.
        $name = 'conversion-'.fake()->unique()->numerify('######');

        return [
            'media_id' => Media::factory(),
            'conversion_name' => $name,
            'driver' => 'local',
            'disk' => 'public',
            'path' => "media/{$this->faker->uuid()}/conversions/{$name}.webp",
            'visibility' => MediaVisibility::Public,
            'mime_type' => 'image/webp',
            'format' => 'webp',
            'width' => 320,
            'height' => 320,
            'size' => fake()->numberBetween(500, 100_000),
        ];
    }
}
