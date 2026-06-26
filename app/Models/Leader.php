<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Leader extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['name', 'role', 'rank', 'bio'];

    /** @var list<string> */
    protected $fillable = [
        'name',
        'role',
        'rank',
        'bio',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Leader>  $query
     * @return Builder<Leader>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 64, 42)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('card')
            ->fit(Fit::Crop, 480, 320)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('hero')
            ->fit(Fit::Crop, 1280, 720)
            ->format('webp')
            ->nonQueued();
    }
}
