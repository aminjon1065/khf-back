<?php

declare(strict_types=1);

namespace App\Modules\Media\Models;

use App\Modules\Media\Enums\MediaVisibility;
use Database\Factories\Modules\Media\MediaConversionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A derived file generated from a {@see Media} original (thumbnail, WebP/AVIF,
 * responsive width, …).
 *
 * @property string $id
 * @property string $media_id
 * @property string $conversion_name
 * @property string $driver
 * @property string $disk
 * @property string $path
 * @property MediaVisibility $visibility
 * @property string $mime_type
 * @property string $format
 * @property int|null $width
 * @property int|null $height
 * @property int $size
 */
class MediaConversion extends Model
{
    /** @use HasFactory<MediaConversionFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'media_id',
        'conversion_name',
        'driver',
        'disk',
        'path',
        'visibility',
        'mime_type',
        'format',
        'width',
        'height',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => MediaVisibility::class,
            'width' => 'integer',
            'height' => 'integer',
            'size' => 'integer',
        ];
    }

    /** @return BelongsTo<Media, $this> */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    protected static function newFactory(): MediaConversionFactory
    {
        return MediaConversionFactory::new();
    }
}
