<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface ModuleInterface
{
    /**
     * Bind the module's services into the container.
     *
     * Called during the service-provider register phase — no side effects allowed here.
     */
    public function register(): void;

    /**
     * Bootstrap the module: routes, event listeners, scheduled commands.
     *
     * Called during the service-provider boot phase, after all register() calls complete.
     */
    public function boot(): void;
}
