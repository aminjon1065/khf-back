<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ForumTopic extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['title'];

    /** @var list<string> */
    protected $fillable = [
        'forum_category_id',
        'title',
        'author',
        'replies',
        'views',
        'pinned',
        'last_activity',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'replies' => 'integer',
            'views' => 'integer',
            'pinned' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<ForumCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    /**
     * @param  Builder<ForumTopic>  $query
     * @return Builder<ForumTopic>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
