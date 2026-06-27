<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EntryResource;
use Illuminate\Http\JsonResponse;

class ActivitiesController extends Controller
{
    /**
     * GET /api/v1/activities — направления деятельности и программы.
     */
    public function index(): JsonResponse
    {
        $locale = app()->getLocale();

        $directions = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'directions'))
            ->get()
            ->sortBy(fn ($e) => $e->data[$locale]['sort_order'] ?? 0)
            ->values();

        $programs = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'programs'))
            ->get()
            ->sortBy(fn ($e) => $e->data[$locale]['sort_order'] ?? 0)
            ->values();

        return response()->json([
            'data' => [
                'directions' => EntryResource::collection($directions),
                'programs' => EntryResource::collection($programs),
            ],
        ]);
    }
}
