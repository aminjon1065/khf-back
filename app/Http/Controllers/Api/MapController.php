<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    /**
     * GET /api/v1/map — оперативная карта: регионы + сводная статистика.
     */
    public function index(): JsonResponse
    {
        $regions = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'regions'))
            ->get()
            ->sortBy(fn ($e) => $e->data['global']['sort_order'] ?? 0)
            ->values();

        return response()->json([
            'data' => [
                'regions' => RegionResource::collection($regions),
                'stats' => Setting::get('map_stats'),
            ],
        ]);
    }
}
