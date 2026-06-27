<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface PluginInterface
{
    /**
     * Bind the plugin's services into the container.
     *
     * Called during the service-provider register phase — no side effects allowed here.
     */
    public function register(): void;

    /**
     * Bootstrap the plugin: hooks, event listeners, admin menu items.
     *
     * Called during the service-provider boot phase, after all register() calls complete.
     */
    public function boot(): void;

    /**
     * The unique machine-readable name used to identify this plugin in the registry.
     *
     * Convention: vendor/package-name (e.g. "acme/seo-plugin").
     */
    public function getName(): string;

    /**
     * The semantic version of the plugin (e.g. "1.0.0").
     */
    public function getVersion(): string;
}
