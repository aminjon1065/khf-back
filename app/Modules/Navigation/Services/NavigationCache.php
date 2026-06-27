<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Services;

use App\Modules\Navigation\Contracts\NavigationCacheInterface;
use App\Modules\Navigation\Contracts\NavigationTreeBuilderInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Caches the fully-built, multi-locale navigation trees so reads avoid the
 * database. Adds an in-request memo on top of the cache store so repeated reads
 * in one request touch neither the cache nor the database more than once.
 */
final class NavigationCache implements NavigationCacheInterface
{
    /** @var array<string, list<array<string, mixed>>>|null */
    private ?array $memo = null;

    public function __construct(
        private readonly NavigationTreeBuilderInterface $builder,
        private readonly CacheRepository $cache,
        private readonly string $cacheKey,
        private readonly int $ttl,
    ) {}

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public function all(): array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        $loader = fn (): array => $this->builder->buildAll();

        return $this->memo = $this->ttl > 0
            ? $this->cache->remember($this->cacheKey, $this->ttl, $loader)
            : $this->cache->rememberForever($this->cacheKey, $loader);
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    public function get(string $handle): ?array
    {
        return $this->all()[$handle] ?? null;
    }

    public function warm(): void
    {
        $this->flush();
        $this->all();
    }

    public function flush(): void
    {
        $this->memo = null;
        $this->cache->forget($this->cacheKey);
    }
}
