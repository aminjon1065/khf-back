<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(): Response
    {
        $news = News::query()
            ->with('category')
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->through(fn (News $n): array => [
                'id' => $n->id,
                'title' => $n->getTranslation('title', 'ru'),
                'slug' => $n->slug,
                'category' => $n->category?->getTranslation('name', 'ru'),
                'status' => $n->status->value,
                'published_at' => $n->published_at?->format('d.m.Y'),
                'views' => $n->views,
                'thumb' => $n->getFirstMedia('cover')?->getUrl('thumb'),
            ]);

        return Inertia::render('admin/news/index', ['news' => $news]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/news/form', $this->formData());
    }

    public function edit(News $news): Response
    {
        $news->load('category');

        return Inertia::render('admin/news/form', [
            ...$this->formData(),
            'news' => [
                'id' => $news->id,
                'slug' => $news->slug,
                'title' => $news->getTranslations('title'),
                'excerpt' => $news->getTranslations('excerpt'),
                'body' => $news->getTranslations('body'),
                'news_category_id' => $news->news_category_id,
                'author' => $news->author,
                'region' => $news->region,
                'status' => $news->status->value,
                'published_at' => $news->published_at?->format('Y-m-d\TH:i'),
                'cover' => $this->coverUrls($news),
            ],
        ]);
    }

    public function store(NewsRequest $request): RedirectResponse
    {
        $news = News::create($request->payload());
        $this->syncCover($news, $request);

        return to_route('admin.news.index')->with('success', 'Новость создана.');
    }

    public function update(NewsRequest $request, News $news): RedirectResponse
    {
        $news->update($request->payload());
        $this->syncCover($news, $request);

        return to_route('admin.news.index')->with('success', 'Новость обновлена.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return to_route('admin.news.index')->with('success', 'Новость удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
            'categories' => NewsCategory::query()->orderBy('sort_order')->get()
                ->map(fn (NewsCategory $c): array => [
                    'id' => $c->id,
                    'name' => $c->getTranslation('name', 'ru'),
                ]),
            'statuses' => collect(PublishStatus::cases())
                ->map(fn (PublishStatus $s): array => ['value' => $s->value, 'label' => $s->label()]),
        ];
    }

    private function syncCover(News $news, NewsRequest $request): void
    {
        if ($request->hasFile('cover')) {
            $news->clearMediaCollection('cover');
            $news->addMediaFromRequest('cover')->toMediaCollection('cover');
        }
    }

    /**
     * @return array{thumb:string, card:string, hero:string, original:string}|null
     */
    private function coverUrls(News $news): ?array
    {
        $media = $news->getFirstMedia('cover');

        if ($media === null) {
            return null;
        }

        return [
            'thumb' => $media->getUrl('thumb'),
            'card' => $media->getUrl('card'),
            'hero' => $media->getUrl('hero'),
            'original' => $media->getUrl(),
        ];
    }
}
