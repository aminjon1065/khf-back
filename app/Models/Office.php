<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Office extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['region', 'address', 'hours'];

    /** @var list<string> */
    protected $fillable = [
        'region',
        'address',
        'phone',
        'email',
        'hours',
        'is_head',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_head' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Office>  $query
     * @return Builder<Office>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
