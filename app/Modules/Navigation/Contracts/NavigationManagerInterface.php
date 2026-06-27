<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Models\User;
use App\Modules\Navigation\DTOs\NavigationData;
use App\Modules\Navigation\DTOs\NavigationItemData;
use App\Modules\Navigation\DTOs\NavigationTree;
use App\Modules\Navigation\DTOs\NavigationTypeDefinition;
use App\Modules\Navigation\Exceptions\NavigationNotFoundException;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;

/**
 * The public API of the Navigation Engine: resolve menus for a locale + viewer,
 * manage navigations/items, and register pluggable types/sources/generators.
 */
interface NavigationManagerInterface
{
    /**
     * Resolve a navigation into a tree for the given locale (defaults to the
     * active app locale) and viewer (defaults to the authenticated user), with
     * visibility filtering already applied.
     *
     * @throws NavigationNotFoundException when no navigation has the handle.
     */
    public function tree(string $handle, ?string $locale = null, ?User $user = null): NavigationTree;

    public function has(string $handle): bool;

    /**
     * @return list<Navigation>
     */
    public function navigations(): array;

    public function createNavigation(NavigationData $data): Navigation;

    public function updateNavigation(Navigation $navigation, NavigationData $data): Navigation;

    public function deleteNavigation(Navigation $navigation): void;

    public function publishNavigation(Navigation $navigation): Navigation;

    public function addItem(NavigationItemData $data): NavigationItem;

    public function moveItem(NavigationItem $item, ?string $parentId, int $order): NavigationItem;

    public function removeItem(NavigationItem $item): void;

    public function registerType(NavigationTypeDefinition $type): void;

    public function registerSource(NavigationSourceResolverInterface $resolver): void;

    public function registerGenerator(NavigationGeneratorInterface $generator): void;

    public function warm(): void;

    public function flush(): void;
}
