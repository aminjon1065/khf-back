<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ForumCategory extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['title', 'description'];

    /** @var list<string> */
    protected $fillable = [
        'slug',
        'icon',
        'title',
        'description',
        'topics_count',
        'posts_count',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'topics_count' => 'integer',
            'posts_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<ForumTopic, $this>
     */
    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    /**
     * @param  Builder<ForumCategory>  $query
     * @return Builder<ForumCategory>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
