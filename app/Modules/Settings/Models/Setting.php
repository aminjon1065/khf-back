<?php

declare(strict_types=1);

namespace App\Modules\Settings\Models;

use App\Modules\Settings\Models\Scopes\EngineOwnedScope;
use Database\Factories\Modules\Settings\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Persisted setting VALUE (only overridden settings are stored; defaults live in
 * the in-memory registry). Shares the `settings` table with the legacy
 * App\Models\Setting singletons — the columns added by the engine are nullable.
 *
 * A global scope restricts this model to engine-owned rows (namespaced keys),
 * so the engine and the legacy singletons coexist on one table without ever
 * seeing each other's rows.
 *
 * @property int $id
 * @property string $key
 * @property string|null $group
 * @property string|null $type
 * @property mixed $value
 */
#[ScopedBy(EngineOwnedScope::class)]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'group',
        'type',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }
}
