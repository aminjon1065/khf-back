<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Registries;

use App\Modules\Navigation\Contracts\NavigationGeneratorInterface;
use App\Modules\Navigation\Contracts\NavigationGeneratorRegistryInterface;
use App\Modules\Navigation\Exceptions\UnknownNavigationGeneratorException;

final class NavigationGeneratorRegistry implements NavigationGeneratorRegistryInterface
{
    /** @var array<string, NavigationGeneratorInterface> */
    private array $generators = [];

    public function register(NavigationGeneratorInterface $generator): void
    {
        $this->generators[$generator->name()] = $generator;
    }

    public function has(string $name): bool
    {
        return isset($this->generators[$name]);
    }

    public function get(string $name): NavigationGeneratorInterface
    {
        if (! $this->has($name)) {
            throw UnknownNavigationGeneratorException::named($name);
        }

        return $this->generators[$name];
    }

    /**
     * @return array<string, NavigationGeneratorInterface>
     */
    public function all(): array
    {
        return $this->generators;
    }
}
