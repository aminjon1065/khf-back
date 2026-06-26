<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

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
        $url = config('khf.revalidate.url');
        $secret = config('khf.revalidate.secret');

        if (empty($url) || empty($secret)) {
            return;
        }

        try {
            Http::withHeaders(['x-revalidate-secret' => $secret])
                ->timeout(5)
                ->post($url, [
                    'tags' => array_values($tags),
                    'paths' => array_values($paths),
                ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
