<?php

declare(strict_types=1);

namespace App\Core\Hooks;

use App\Core\Contracts\HookManagerInterface;

/**
 * Synchronous, priority-sorted hook system.
 *
 * Two distinct hook types:
 *
 *   Action hooks — fire-and-forget side effects (logging, notifications, indexing).
 *   Filter hooks — transform a value in-flight (mutate an API payload, modify a query).
 *
 * Hooks are stored in priority buckets. ksort() ensures lower numbers run first.
 *
 * Note on callable identity: PHP cannot reliably compare two distinct Closure instances
 * for equality. removeAction() / removeFilter() work correctly when the caller passes
 * the same callable reference that was originally registered.
 */
final class HookManager implements HookManagerInterface
{
    /** @var array<string, array<int, list<callable>>> */
    private array $actions = [];

    /** @var array<string, array<int, list<callable>>> */
    private array $filters = [];

    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
        ksort($this->actions[$hook]);
    }

    public function doAction(string $hook, mixed ...$args): void
    {
        if (! isset($this->actions[$hook])) {
            return;
        }

        foreach ($this->actions[$hook] as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback(...$args);
            }
        }
    }

    public function removeAction(string $hook, callable $callback): void
    {
        $this->removeFromBucket($this->actions, $hook, $callback);
    }

    public function hasAction(string $hook): bool
    {
        return isset($this->actions[$hook]) && $this->actions[$hook] !== [];
    }

    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
        ksort($this->filters[$hook]);
    }

    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        if (! isset($this->filters[$hook])) {
            return $value;
        }

        foreach ($this->filters[$hook] as $callbacks) {
            foreach ($callbacks as $callback) {
                $value = $callback($value, ...$args);
            }
        }

        return $value;
    }

    public function removeFilter(string $hook, callable $callback): void
    {
        $this->removeFromBucket($this->filters, $hook, $callback);
    }

    public function hasFilter(string $hook): bool
    {
        return isset($this->filters[$hook]) && $this->filters[$hook] !== [];
    }

    /**
     * @param  array<string, array<int, list<callable>>>  $bucket
     */
    private function removeFromBucket(array &$bucket, string $hook, callable $callback): void
    {
        if (! isset($bucket[$hook])) {
            return;
        }

        foreach ($bucket[$hook] as $priority => $callbacks) {
            $bucket[$hook][$priority] = array_values(
                array_filter($callbacks, fn (callable $cb): bool => $cb !== $callback),
            );

            if ($bucket[$hook][$priority] === []) {
                unset($bucket[$hook][$priority]);
            }
        }

        if ($bucket[$hook] === []) {
            unset($bucket[$hook]);
        }
    }
}
