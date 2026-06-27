<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EntryResource;
use Illuminate\Http\JsonResponse;

class StructureController extends Controller
{
    /**
     * GET /api/v1/structure — руководство, подразделения и территориальные органы.
     */
    public function index(): JsonResponse
    {
        $locale = app()->getLocale();

        $leaders = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'leaders'))
            ->get()
            ->sortBy(fn ($e) => $e->data[$locale]['sort_order'] ?? 0)
            ->values();

        $departments = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'departments'))
            ->get()
            ->sortBy(fn ($e) => $e->data[$locale]['sort_order'] ?? 0)
            ->values();

        $offices = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'offices'))
            ->get()
            ->sortBy(fn ($e) => $e->data[$locale]['sort_order'] ?? 0)
            ->values();

        return response()->json([
            'data' => [
                'leadership' => EntryResource::collection($leaders),
                'departments' => EntryResource::collection($departments),
                'offices' => EntryResource::collection($offices),
            ],
        ]);
    }
}
