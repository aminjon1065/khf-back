<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Registries;

use App\Modules\Navigation\Contracts\NavigationSourceRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Exceptions\UnknownNavigationSourceException;

final class NavigationSourceRegistry implements NavigationSourceRegistryInterface
{
    /** @var array<string, NavigationSourceResolverInterface> */
    private array $resolvers = [];

    public function register(NavigationSourceResolverInterface $resolver): void
    {
        $this->resolvers[$resolver->type()] = $resolver;
    }

    public function has(string $type): bool
    {
        return isset($this->resolvers[$type]);
    }

    public function get(string $type): NavigationSourceResolverInterface
    {
        if (! $this->has($type)) {
            throw UnknownNavigationSourceException::type($type);
        }

        return $this->resolvers[$type];
    }

    /**
     * @return array<string, NavigationSourceResolverInterface>
     */
    public function all(): array
    {
        return $this->resolvers;
    }
}
