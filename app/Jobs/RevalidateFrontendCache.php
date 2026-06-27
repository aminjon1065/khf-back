<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class RevalidateFrontendCache implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<string>  $tags
     * @param  list<string>  $paths
     */
    public function __construct(
        public array $tags = [],
        public array $paths = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
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
                    'tags' => array_values($this->tags),
                    'paths' => array_values($this->paths),
                ]);
        } catch (Throwable $e) {
            report($e);
            throw $e; // Rethrow to let the queue worker handle retries
        }
    }
}
