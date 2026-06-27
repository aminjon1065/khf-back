<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentCategoryResource;
use App\Http\Resources\DocumentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * GET /api/v1/documents
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'document-categories'))
            ->published()->get()->sortBy('data.global.sort_order');

        $query = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'documents'))
            ->published();

        $categorySlug = $request->string('category')->toString();
        if ($categorySlug !== '') {
            $catEntry = Entry::whereHas('collection', fn ($q) => $q->where('slug', 'document-categories'))
                ->where('slug', $categorySlug)->first();
            if ($catEntry) {
                $query->where('data->global->category_id', $catEntry->id);
            } else {
                $query->where('id', null);
            }
        }

        $items = $query->get()->sortBy('data.global.sort_order');

        return response()->json([
            'data' => [
                'categories' => DocumentCategoryResource::collection($categories),
                'items' => DocumentResource::collection($items->values()),
            ],
        ]);
    }
}
