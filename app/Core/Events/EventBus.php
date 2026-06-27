<?php

declare(strict_types=1);

namespace App\Core\Events;

use App\Core\Contracts\EventBusInterface;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * EventBus implementation backed by Laravel's event dispatcher.
 *
 * This is a thin wrapper that adds the platform's EventBusInterface vocabulary
 * without replacing any Laravel internals. All existing Laravel event subscribers,
 * Model observers, and Eloquent events continue to work unchanged.
 *
 * Domain events should implement ShouldQueue on their listeners to protect
 * write-path latency (e.g. ISR revalidation, search indexing, email notifications).
 */
final class EventBus implements EventBusInterface
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function listen(string $event, \Closure|array|string $listener): void
    {
        $this->dispatcher->listen($event, $listener);
    }
}
