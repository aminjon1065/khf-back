<?php

declare(strict_types=1);

namespace App\Modules\Settings\Services;

use App\Modules\Settings\Contracts\SettingsCacheInterface;
use App\Modules\Settings\Contracts\SettingsRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Caches the full persisted-value map so reads avoid the database. Adds an
 * in-request memo on top of the cache store so repeated reads in one request
 * touch neither the cache nor the database more than once.
 */
final class SettingsCache implements SettingsCacheInterface
{
    /** @var array<string, mixed>|null */
    private ?array $memo = null;

    public function __construct(
        private readonly SettingsRepositoryInterface $repository,
        private readonly CacheRepository $cache,
        private readonly string $cacheKey,
        private readonly int $ttl,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function values(): array
    {
        if ($this->memo !== null) {
            return $this->memo;
        }

        $loader = fn (): array => $this->repository->all();

        return $this->memo = $this->ttl > 0
            ? $this->cache->remember($this->cacheKey, $this->ttl, $loader)
            : $this->cache->rememberForever($this->cacheKey, $loader);
    }

    public function warm(): void
    {
        $this->flush();
        $this->values();
    }

    public function flush(): void
    {
        $this->memo = null;
        $this->cache->forget($this->cacheKey);
    }
}
