<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    /**
     * GET /api/v1/news
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 12), 1), 50);

        $query = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'news'))
            ->published()
            ->latest('published_at');

        $category = $request->string('category')->toString();
        if ($category !== '') {
            $catEntry = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'news-categories'))
                ->where('slug', $category)->first();
            if ($catEntry) {
                $query->where('data->global->category_id', $catEntry->id);
            } else {
                $query->where('id', null); // force empty
            }
        }

        $search = $request->string('search')->toString();
        if ($search !== '') {
            $locale = app()->getLocale();
            $query->where("data->{$locale}->title", 'like', '%'.$search.'%');
        }

        return NewsResource::collection($query->paginate($perPage));
    }

    /**
     * GET /api/v1/news/{slug}
     */
    public function show(string $slug): NewsResource
    {
        $news = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'news'))
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new NewsResource($news);
    }

    /**
     * GET /api/v1/news/{slug}/related
     */
    public function related(Request $request, string $slug): AnonymousResourceCollection
    {
        $news = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'news'))
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $limit = min(max($request->integer('limit', 3), 1), 12);

        $catId = $news->data['global']['category_id'] ?? null;

        $related = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'news'))
            ->published()
            ->where('id', '!=', $news->id)
            ->when($catId, fn ($q) => $q->where('data->global->category_id', $catId))
            ->latest('published_at')
            ->limit($limit)
            ->get();

        if ($related->count() < $limit) {
            $more = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'news'))
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
