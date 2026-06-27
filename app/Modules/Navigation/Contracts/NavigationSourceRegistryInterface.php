<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Modules\Navigation\Exceptions\UnknownNavigationSourceException;

/**
 * Registry of navigation source resolvers, keyed by their type() name.
 */
interface NavigationSourceRegistryInterface
{
    public function register(NavigationSourceResolverInterface $resolver): void;

    public function has(string $type): bool;

    /**
     * @throws UnknownNavigationSourceException when no resolver is registered for the type.
     */
    public function get(string $type): NavigationSourceResolverInterface;

    /**
     * @return array<string, NavigationSourceResolverInterface>
     */
    public function all(): array;
}
