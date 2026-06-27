<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Support;

/**
 * The extension-point catalogue for the Navigation Engine. Modules and plugins
 * use these hook names to register types/sources/generators and to reshape menu
 * trees.
 */
final class NavigationHooks
{
    /**
     * Action — fired once during boot with the NavigationManager so listeners can
     * register navigation types, sources and generators.
     */
    public const string REGISTER = 'khf.navigation.register';

    /**
     * Filter — receives the resolved, visibility-filtered list of root
     * NavigationNode objects (plus the Navigation, locale and ?user as context)
     * so listeners may modify the tree or inject items before it is returned.
     *
     * @filtered list<\App\Modules\Navigation\DTOs\NavigationNode>
     */
    public const string MODIFY_TREE = 'khf.navigation.tree';

    /**
     * Filter — resolves the URL for a `module_page` source. Receives a null URL
     * and the page key + locale; a module returns the resolved URL string.
     *
     * @filtered string|null
     */
    public const string MODULE_PAGE_URL = 'khf.navigation.module_page';

    /**
     * Filter — resolves the URL for a `plugin_page` source. Receives a null URL
     * and the page key + locale; a plugin returns the resolved URL string.
     *
     * @filtered string|null
     */
    public const string PLUGIN_PAGE_URL = 'khf.navigation.plugin_page';

    private function __construct() {}
}
