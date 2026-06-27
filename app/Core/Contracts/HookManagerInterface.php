<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface HookManagerInterface
{
    /**
     * Register a callback to run when an action hook fires.
     *
     * Action hooks execute side effects sequentially — they do not return a value.
     * Lower priority numbers run first.
     */
    public function addAction(string $hook, callable $callback, int $priority = 10): void;

    /**
     * Fire all callbacks registered on an action hook.
     */
    public function doAction(string $hook, mixed ...$args): void;

    /**
     * Remove a previously registered action callback.
     *
     * The callable reference must be identical to the one passed to addAction().
     */
    public function removeAction(string $hook, callable $callback): void;

    /**
     * Return true if at least one callback is registered on the action hook.
     */
    public function hasAction(string $hook): bool;

    /**
     * Register a callback to transform a value on a filter hook.
     *
     * Filter hooks allow plugins to modify data in-flight.
     * Lower priority numbers run first.
     */
    public function addFilter(string $hook, callable $callback, int $priority = 10): void;

    /**
     * Pass $value through all callbacks registered on a filter hook and return the result.
     */
    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed;

    /**
     * Remove a previously registered filter callback.
     *
     * The callable reference must be identical to the one passed to addFilter().
     */
    public function removeFilter(string $hook, callable $callback): void;

    /**
     * Return true if at least one callback is registered on the filter hook.
     */
    public function hasFilter(string $hook): bool;
}
