<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Facades;

use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Services\NavigationManager;
use Illuminate\Support\Facades\Facade;

/**
 * Static entry point to the Navigation Engine.
 *
 * @method static \App\Modules\Navigation\DTOs\NavigationTree tree(string $handle, ?string $locale = null, ?\App\Models\User $user = null)
 * @method static bool has(string $handle)
 * @method static list<\App\Modules\Navigation\Models\Navigation> navigations()
 * @method static \App\Modules\Navigation\Models\Navigation createNavigation(\App\Modules\Navigation\DTOs\NavigationData $data)
 * @method static \App\Modules\Navigation\Models\Navigation updateNavigation(\App\Modules\Navigation\Models\Navigation $navigation, \App\Modules\Navigation\DTOs\NavigationData $data)
 * @method static void deleteNavigation(\App\Modules\Navigation\Models\Navigation $navigation)
 * @method static \App\Modules\Navigation\Models\Navigation publishNavigation(\App\Modules\Navigation\Models\Navigation $navigation)
 * @method static \App\Modules\Navigation\Models\NavigationItem addItem(\App\Modules\Navigation\DTOs\NavigationItemData $data)
 * @method static \App\Modules\Navigation\Models\NavigationItem moveItem(\App\Modules\Navigation\Models\NavigationItem $item, ?string $parentId, int $order)
 * @method static void removeItem(\App\Modules\Navigation\Models\NavigationItem $item)
 * @method static void registerType(\App\Modules\Navigation\DTOs\NavigationTypeDefinition $type)
 * @method static void registerSource(\App\Modules\Navigation\Contracts\NavigationSourceResolverInterface $resolver)
 * @method static void registerGenerator(\App\Modules\Navigation\Contracts\NavigationGeneratorInterface $generator)
 * @method static void warm()
 * @method static void flush()
 *
 * @see NavigationManager
 */
final class Navigation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NavigationManagerInterface::class;
    }
}
