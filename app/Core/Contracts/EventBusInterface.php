<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface EventBusInterface
{
    /**
     * Dispatch a domain event to all registered listeners.
     *
     * Listeners registered via listen() receive the event object.
     * Queued listeners execute asynchronously, protecting write-path latency.
     */
    public function dispatch(object $event): void;

    /**
     * Register a listener for a domain event class.
     *
     * $listener may be:
     *   - A fully-qualified listener class name (resolved from the container).
     *   - A Closure for inline listeners.
     *   - An array [$object, 'methodName'] for method listeners.
     *
     * @param  \Closure|array<int, mixed>|string  $listener
     */
    public function listen(string $event, \Closure|array|string $listener): void;
}
