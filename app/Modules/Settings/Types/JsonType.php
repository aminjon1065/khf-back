<?php

declare(strict_types=1);

namespace App\Modules\Settings\Types;

use App\Modules\Settings\Enums\SettingType;

/**
 * Any JSON-serialisable value (object, array or scalar). Stored and read as-is.
 */
final class JsonType extends AbstractSettingType
{
    public function name(): string
    {
        return SettingType::Json->value;
    }
}
