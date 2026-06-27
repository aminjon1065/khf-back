<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Sources;

use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Models\NavigationItem;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * Resolves a Laravel named route (stored in source_value) to a relative URL. The
 * active locale is passed only when the route actually declares a {locale}
 * parameter, so locale-agnostic routes are not polluted with a query string.
 */
final class RouteSourceResolver implements NavigationSourceResolverInterface
{
    public function type(): string
    {
        return NavigationSourceType::Route->value;
    }

    public function resolve(NavigationItem $item, string $locale): ?string
    {
        $name = $item->source_value;

        if ($name === null || $name === '' || ! Route::has($name)) {
            return null;
        }

        $route = Route::getRoutes()->getByName($name);
        $parameters = ($route !== null && in_array('locale', $route->parameterNames(), true))
            ? ['locale' => $locale]
            : [];

        try {
            return route($name, $parameters, false);
        } catch (Throwable) {
            return null;
        }
    }
}
