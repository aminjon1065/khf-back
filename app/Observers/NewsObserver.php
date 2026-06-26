<?php

namespace App\Observers;

use App\Models\News;
use App\Services\NextRevalidator;

class NewsObserver
{
    public function __construct(private NextRevalidator $revalidator) {}

    public function saved(News $news): void
    {
        $this->flush($news);
    }

    public function deleted(News $news): void
    {
        $this->flush($news);
    }

    private function flush(News $news): void
    {
        $this->revalidator->revalidate(
            tags: ['news', 'news:'.$news->slug, 'home'],
        );
    }
}
