<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['title', 'subtitle'];

    /** @var list<string> */
    protected $fillable = [
        'key',
        'icon',
        'title',
        'subtitle',
        'is_primary',
        'tel',
        'route_key',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
