<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Support;

/**
 * The content locales the engine resolves navigation labels and URLs for, read
 * once from config so the builder and generators agree on the set.
 */
final class NavigationLocales
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        $locales = config('khf.locales', ['tg']);

        return is_array($locales)
            ? array_values(array_map(strval(...), $locales))
            : ['tg'];
    }

    private function __construct() {}
}
