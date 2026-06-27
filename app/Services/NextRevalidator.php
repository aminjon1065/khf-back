<?php

namespace App\Services;

use App\Jobs\RevalidateFrontendCache;

/**
 * Дёргает вебхук ре-валидации Next (ISR) при изменении контента.
 * Сбой не валит основную операцию (контент уже сохранён).
 */
class NextRevalidator
{
    /**
     * @param  list<string>  $tags
     * @param  list<string>  $paths
     */
    public function revalidate(array $tags = [], array $paths = []): void
    {
        RevalidateFrontendCache::dispatch($tags, $paths);
    }
}
