<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Media;

use App\Modules\Media\Enums\MediaVisibility;
use App\Modules\Media\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
final class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $slug = fake()->unique()->slug(2);

        return [
            'driver' => 'local',
            'disk' => 'public',
            'path' => "media/{$slug}/{$slug}.jpg",
            'visibility' => MediaVisibility::Public,
            'name' => fake()->sentence(3),
            'file_name' => "{$slug}.jpg",
            'original_file_name' => "{$slug}.jpg",
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => fake()->numberBetween(1_000, 5_000_000),
            'width' => 800,
            'height' => 600,
            'duration' => null,
            'checksum' => hash('sha256', (string) fake()->unique()->uuid()),
            'alt_text' => null,
            'caption' => null,
            'copyright' => null,
            'focal_point' => null,
            'dominant_color' => '#336699',
            'exif' => null,
            'custom_properties' => null,
            'uploaded_by' => null,
        ];
    }

    public function private(): static
    {
        return $this->state(fn (): array => [
            'visibility' => MediaVisibility::Private,
            'disk' => 'local',
        ]);
    }

    public function document(): static
    {
        return $this->state(fn (): array => [
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'width' => null,
            'height' => null,
        ]);
    }
}
