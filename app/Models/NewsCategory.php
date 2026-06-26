<?php

namespace App\Models;

use App\Enums\Tone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class NewsCategory extends Model
{
    use HasFactory, HasTranslations;

    /** @var list<string> */
    public array $translatable = ['name'];

    /** @var list<string> */
    protected $fillable = ['slug', 'name', 'tone', 'sort_order'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tone' => Tone::class,
        ];
    }

    /**
     * @return HasMany<News, $this>
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
