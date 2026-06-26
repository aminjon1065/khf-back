<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForumCategoryResource;
use App\Http\Resources\ForumTopicResource;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class ForumController extends Controller
{
    /**
     * GET /api/v1/forum — категории, темы и статистика форума.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'categories' => ForumCategoryResource::collection(ForumCategory::ordered()->get()),
                'topics' => ForumTopicResource::collection(
                    ForumTopic::query()->with('category')->orderByDesc('pinned')->ordered()->get()
                ),
                'stats' => Setting::get('forum_stats'),
            ],
        ]);
    }
}
