<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\ForumCategoryResource;
use App\Http\Resources\ForumTopicResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class ForumController extends Controller
{
    /**
     * GET /api/v1/forum — категории, темы и статистика форума.
     */
    public function index(): JsonResponse
    {
        $categories = Entry::whereHas('collection', function ($q) {
            $q->where('slug', 'forum-categories');
        })->published()->get()->sortBy('data.global.sort_order');

        $topics = Entry::whereHas('collection', function ($q) {
            $q->where('slug', 'forum-topics');
        })->published()->get()->sortBy([
            ['data.global.pinned', 'desc'],
            ['data.global.sort_order', 'asc'],
        ]);

        return response()->json([
            'data' => [
                'categories' => ForumCategoryResource::collection($categories->values()),
                'topics' => ForumTopicResource::collection($topics->values()),
                'stats' => Setting::get('forum_stats'),
            ],
        ]);
    }
}
