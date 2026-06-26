<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForumTopicRequest;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ForumTopicController extends Controller
{
    public function index(): Response
    {
        $items = ForumTopic::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (ForumTopic $t): array => [
                'id' => $t->id,
                'title' => $t->getTranslation('title', 'ru'),
                'author' => $t->author,
                'pinned' => $t->pinned,
                'replies' => $t->replies,
            ]);

        return Inertia::render('admin/forum-topics/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/forum-topics/form', $this->formData());
    }

    public function edit(ForumTopic $forumTopic): Response
    {
        return Inertia::render('admin/forum-topics/form', [
            ...$this->formData(),
            'item' => [
                'id' => $forumTopic->id,
                'title' => $forumTopic->getTranslations('title'),
                'forum_category_id' => $forumTopic->forum_category_id,
                'author' => $forumTopic->author,
                'replies' => $forumTopic->replies,
                'views' => $forumTopic->views,
                'pinned' => $forumTopic->pinned,
                'last_activity' => $forumTopic->last_activity,
                'sort_order' => $forumTopic->sort_order,
            ],
        ]);
    }

    public function store(ForumTopicRequest $request): RedirectResponse
    {
        ForumTopic::create($request->payload());

        return to_route('admin.forum-topics.index')->with('success', 'Тема форума создана.');
    }

    public function update(ForumTopicRequest $request, ForumTopic $forumTopic): RedirectResponse
    {
        $forumTopic->update($request->payload());

        return to_route('admin.forum-topics.index')->with('success', 'Тема форума обновлена.');
    }

    public function destroy(ForumTopic $forumTopic): RedirectResponse
    {
        $forumTopic->delete();

        return to_route('admin.forum-topics.index')->with('success', 'Тема форума удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
            'forumCategories' => ForumCategory::query()->orderBy('sort_order')->orderBy('id')->get()
                ->map(fn (ForumCategory $c): array => [
                    'id' => $c->id,
                    'name' => $c->getTranslation('title', 'ru'),
                ]),
        ];
    }
}
