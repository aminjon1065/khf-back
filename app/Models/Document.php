<?php

namespace App\Models;

use App\Enums\DocType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Document extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    /** @var list<string> */
    public array $translatable = ['title'];

    /** @var list<string> */
    protected $fillable = [
        'document_category_id',
        'slug',
        'title',
        'number',
        'document_date',
        'type',
        'size',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DocType::class,
            'document_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<DocumentCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    /**
     * @param  Builder<Document>  $query
     * @return Builder<Document>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')->singleFile();
    }
}
