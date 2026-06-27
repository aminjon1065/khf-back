<?php

namespace App\Http\Controllers\Api;

use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Http\Resources\HotlineResource;
use App\Http\Resources\OfficeResource;
use Illuminate\Http\JsonResponse;

class ContactsController extends Controller
{
    /**
     * GET /api/v1/contacts — агрегат страницы «Тамос»:
     * горячие линии, головной офис и региональные представительства.
     */
    public function index(): JsonResponse
    {
        $headOffice = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'offices'))
            ->get()
            ->firstWhere('data.global.is_head', true);

        $offices = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'offices'))
            ->get()
            ->reject(fn ($e) => $e->data['global']['is_head'] ?? false)
            ->sortBy(fn ($e) => $e->data['global']['sort_order'] ?? 0)
            ->values();

        $hotlines = Entry::published()
            ->whereHas('collection', fn ($q) => $q->where('slug', 'hotlines'))
            ->get()
            ->sortBy(fn ($e) => $e->data['global']['sort_order'] ?? 0)
            ->values();

        return response()->json([
            'data' => [
                'hotlines' => HotlineResource::collection($hotlines),
                'headOffice' => $headOffice ? new OfficeResource($headOffice) : null,
                'offices' => OfficeResource::collection($offices),
            ],
        ]);
    }
}
