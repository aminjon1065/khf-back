<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Hotline extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['label', 'note'];

    /** @var list<string> */
    protected $fillable = [
        'number',
        'label',
        'note',
        'is_primary',
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
     * @param  Builder<Hotline>  $query
     * @return Builder<Hotline>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
