<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Key-value настройки/синглтоны (president, site_stats, forum_stats, map_stats).
 * Значение — JSON; переводимые поля хранятся как {tg,ru,en} внутри value.
 */
class Setting extends Model
{
    /** @var list<string> */
    protected $fillable = ['key', 'value'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
