<?php

declare(strict_types=1);

namespace App\Modules\Media\Models;

use App\Models\User;
use App\Modules\Media\Enums\MediaType;
use App\Modules\Media\Enums\MediaVisibility;
use Database\Factories\Modules\Media\MediaFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Canonical DAM asset for the KHF Media Engine.
 *
 * @property string $id
 * @property string $driver
 * @property string $disk
 * @property string $path
 * @property MediaVisibility $visibility
 * @property string|null $name
 * @property string $file_name
 * @property string $original_file_name
 * @property string $mime_type
 * @property string|null $extension
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property int|null $duration
 * @property string|null $checksum
 * @property string|null $alt_text
 * @property string|null $caption
 * @property string|null $copyright
 * @property array{x: float, y: float}|null $focal_point
 * @property string|null $dominant_color
 * @property array<string, mixed>|null $exif
 * @property array<string, mixed>|null $custom_properties
 * @property int|null $uploaded_by
 * @property Carbon|null $deleted_at
 */
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'driver',
        'disk',
        'path',
        'visibility',
        'name',
        'file_name',
        'original_file_name',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'duration',
        'checksum',
        'alt_text',
        'caption',
        'copyright',
        'focal_point',
        'dominant_color',
        'exif',
        'custom_properties',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => MediaVisibility::class,
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration' => 'integer',
            'focal_point' => 'array',
            'exif' => 'array',
            'custom_properties' => 'array',
        ];
    }

    public function type(): MediaType
    {
        return MediaType::fromMimeType($this->mime_type);
    }

    /** @return HasMany<MediaConversion, $this> */
    public function conversions(): HasMany
    {
        return $this->hasMany(MediaConversion::class);
    }

    /** @return BelongsTo<User, $this> */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    protected static function newFactory(): MediaFactory
    {
        return MediaFactory::new();
    }
}
