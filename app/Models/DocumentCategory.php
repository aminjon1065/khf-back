<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class DocumentCategory extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['name'];

    /** @var list<string> */
    protected $fillable = ['slug', 'name', 'sort_order'];

    /**
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * @param  Builder<DocumentCategory>  $query
     * @return Builder<DocumentCategory>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
