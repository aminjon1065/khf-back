<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Department extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['title', 'description', 'head'];

    /** @var list<string> */
    protected $fillable = [
        'icon',
        'title',
        'description',
        'head',
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
     * @param  Builder<Department>  $query
     * @return Builder<Department>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
