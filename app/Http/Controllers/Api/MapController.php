<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    /**
     * GET /api/v1/map — оперативная карта: регионы + сводная статистика.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'regions' => RegionResource::collection(Region::ordered()->get()),
                'stats' => Setting::get('map_stats'),
            ],
        ]);
    }
}
