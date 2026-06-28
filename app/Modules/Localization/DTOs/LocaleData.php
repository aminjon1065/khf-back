<?php

declare(strict_types=1);

namespace App\Modules\Localization\DTOs;

use App\Modules\Localization\Enums\TextDirection;

/**
 * The immutable input payload for creating or updating a locale. Carries the
 * camelCase shape used by callers and maps it to the snake_case model columns.
 */
final class LocaleData
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $nativeName,
        public readonly TextDirection $direction = TextDirection::Ltr,
        public readonly bool $isDefault = false,
        public readonly bool $isActive = true,
        public readonly ?string $fallbackCode = null,
        public readonly ?string $alias = null,
        public readonly int $sortOrder = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            nativeName: (string) ($data['native_name'] ?? ($data['nativeName'] ?? '')),
            direction: TextDirection::from((string) ($data['direction'] ?? 'ltr')),
            isDefault: (bool) ($data['is_default'] ?? ($data['isDefault'] ?? false)),
            isActive: (bool) ($data['is_active'] ?? ($data['isActive'] ?? true)),
            fallbackCode: isset($data['fallback_code']) ? (string) $data['fallback_code'] : (isset($data['fallbackCode']) ? (string) $data['fallbackCode'] : null),
            alias: isset($data['alias']) ? (string) $data['alias'] : null,
            sortOrder: (int) ($data['sort_order'] ?? ($data['sortOrder'] ?? 0)),
        );
    }

    /**
     * Map to the snake_case `locales` table columns.
     *
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'native_name' => $this->nativeName,
            'direction' => $this->direction->value,
            'is_default' => $this->isDefault,
            'is_active' => $this->isActive,
            'fallback_code' => $this->fallbackCode,
            'alias' => $this->alias,
            'sort_order' => $this->sortOrder,
        ];
    }
}
