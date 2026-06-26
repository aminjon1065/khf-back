<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Observers\NewsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

#[ObservedBy([NewsObserver::class])]
class News extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected $table = 'news';

    /** @var list<string> */
    public array $translatable = ['title', 'excerpt', 'body'];

    /** @var list<string> */
    protected $fillable = [
        'news_category_id',
        'slug',
        'title',
        'excerpt',
        'body',
        'author',
        'region',
        'views',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
            'published_at' => 'datetime',
            'views' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<NewsCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    /**
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published)
            ->where('published_at', '<=', now());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Очень маленькое превью для быстрой загрузки списков (thumb_image).
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
