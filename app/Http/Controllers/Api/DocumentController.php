<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * GET /api/v1/documents — категории + документы (опциональный фильтр ?category).
     */
    public function index(Request $request): JsonResponse
    {
        $categories = DocumentCategory::ordered()->withCount('documents')->get();

        $query = Document::query()->with('category')->ordered();

        $category = $request->string('category')->toString();
        if ($category !== '') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        return response()->json([
            'data' => [
                'categories' => DocumentCategoryResource::collection($categories),
                'items' => DocumentResource::collection($query->get()),
            ],
        ]);
    }
}
