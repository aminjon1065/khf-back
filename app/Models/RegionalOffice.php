<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class RegionalOffice extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['region', 'head', 'address'];

    /** @var list<string> */
    protected $fillable = [
        'region',
        'head',
        'phone',
        'address',
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
     * @param  Builder<RegionalOffice>  $query
     * @return Builder<RegionalOffice>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
