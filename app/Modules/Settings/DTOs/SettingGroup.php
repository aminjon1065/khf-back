<?php

declare(strict_types=1);

namespace App\Modules\Settings\DTOs;

/**
 * A logical grouping of settings (General, Branding, SEO, …). Modules register
 * their own groups during boot.
 */
final class SettingGroup
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $label = null,
        public readonly ?string $description = null,
    ) {}
}
