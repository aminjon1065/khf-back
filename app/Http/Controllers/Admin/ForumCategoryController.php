<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForumCategoryRequest;
use App\Models\ForumCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ForumCategoryController extends Controller
{
    public function index(): Response
    {
        $items = ForumCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (ForumCategory $c): array => [
                'id' => $c->id,
                'title' => $c->getTranslation('title', 'ru'),
                'slug' => $c->slug,
                'topics_count' => $c->topics_count,
            ]);

        return Inertia::render('admin/forum-categories/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/forum-categories/form', $this->formData());
    }

    public function edit(ForumCategory $forumCategory): Response
    {
        return Inertia::render('admin/forum-categories/form', [
            ...$this->formData(),
            'item' => [
                'id' => $forumCategory->id,
                'title' => $forumCategory->getTranslations('title'),
                'description' => $forumCategory->getTranslations('description'),
                'slug' => $forumCategory->slug,
                'icon' => $forumCategory->icon,
                'topics_count' => $forumCategory->topics_count,
                'posts_count' => $forumCategory->posts_count,
                'sort_order' => $forumCategory->sort_order,
            ],
        ]);
    }

    public function store(ForumCategoryRequest $request): RedirectResponse
    {
        ForumCategory::create($request->payload());

        return to_route('admin.forum-categories.index')->with('success', 'Категория форума создана.');
    }

    public function update(ForumCategoryRequest $request, ForumCategory $forumCategory): RedirectResponse
    {
        $forumCategory->update($request->payload());

        return to_route('admin.forum-categories.index')->with('success', 'Категория форума обновлена.');
    }

    public function destroy(ForumCategory $forumCategory): RedirectResponse
    {
        $forumCategory->delete();

        return to_route('admin.forum-categories.index')->with('success', 'Категория форума удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
        ];
    }
}
