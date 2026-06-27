<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Contracts;

use App\Modules\Navigation\Exceptions\UnknownNavigationGeneratorException;

/**
 * Registry of dynamic navigation generators, keyed by their name().
 */
interface NavigationGeneratorRegistryInterface
{
    public function register(NavigationGeneratorInterface $generator): void;

    public function has(string $name): bool;

    /**
     * @throws UnknownNavigationGeneratorException when no generator is registered with the name.
     */
    public function get(string $name): NavigationGeneratorInterface;

    /**
     * @return array<string, NavigationGeneratorInterface>
     */
    public function all(): array;
}
