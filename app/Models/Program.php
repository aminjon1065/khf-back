<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Program extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['title', 'description'];

    /** @var list<string> */
    protected $fillable = [
        'title',
        'description',
        'period',
        'status',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProgramStatus::class,
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<Program>  $query
     * @return Builder<Program>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
