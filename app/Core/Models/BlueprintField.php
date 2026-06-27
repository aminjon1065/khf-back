<?php

declare(strict_types=1);

namespace App\Core\Models;

use App\Core\Enums\FieldType;
use Database\Factories\Core\BlueprintFieldFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $blueprint_id
 * @property string $name
 * @property string $handle
 * @property FieldType $type
 * @property bool $is_translatable
 * @property array<string, mixed>|null $validation_rules
 * @property array<string, mixed>|null $settings
 * @property int $order
 */
class BlueprintField extends Model
{
    /** @use HasFactory<BlueprintFieldFactory> */
    use HasFactory;

    use HasUuids;

    protected $table = 'fields';

    protected $fillable = [
        'blueprint_id',
        'name',
        'handle',
        'type',
        'is_translatable',
        'validation_rules',
        'settings',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'type' => FieldType::class,
            'is_translatable' => 'boolean',
            'validation_rules' => 'array',
            'settings' => 'array',
            'order' => 'integer',
        ];
    }

    /** @return BelongsTo<Blueprint, $this> */
    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Blueprint::class);
    }

    protected static function newFactory(): BlueprintFieldFactory
    {
        return BlueprintFieldFactory::new();
    }
}
