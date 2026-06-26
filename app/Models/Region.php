<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['name', 'center', 'note'];

    /** @var list<string> */
    protected $fillable = [
        'slug',
        'name',
        'center',
        'risk',
        'active_incidents',
        'stations',
        'note',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'risk' => RiskLevel::class,
            'active_incidents' => 'integer',
            'stations' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Region>  $query
     * @return Builder<Region>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
