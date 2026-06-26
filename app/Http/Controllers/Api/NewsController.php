<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    /**
     * GET /api/v1/news — лента (пагинация Laravel) с фильтром/поиском.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 50);

        $query = News::query()
            ->with('category')
            ->published()
            ->latest('published_at');

        $category = $request->string('category')->toString();
        if ($category !== '') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        $search = $request->string('search')->toString();
        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        return NewsResource::collection($query->paginate($perPage));
    }

    /**
     * GET /api/v1/news/{slug} — одна опубликованная статья.
     */
    public function show(string $slug): NewsResource
    {
        $news = News::query()
            ->with('category')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new NewsResource($news);
    }

    /**
     * GET /api/v1/news/{slug}/related — похожие новости.
     */
    public function related(Request $request, string $slug): AnonymousResourceCollection
    {
        $news = News::query()->published()->where('slug', $slug)->firstOrFail();
        $limit = min(max($request->integer('limit', 3), 1), 12);

        $related = News::query()
            ->with('category')
            ->published()
            ->where('id', '!=', $news->id)
            ->when($news->news_category_id, fn ($q) => $q->where('news_category_id', $news->news_category_id))
            ->latest('published_at')
            ->limit($limit)
            ->get();

        if ($related->count() < $limit) {
            $more = News::query()
                ->with('category')
                ->published()
                ->where('id', '!=', $news->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->latest('published_at')
                ->limit($limit - $related->count())
                ->get();

            $related = $related->concat($more);
        }

        return NewsResource::collection($related);
    }
}
