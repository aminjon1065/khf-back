<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotlineResource;
use App\Http\Resources\OfficeResource;
use App\Models\Hotline;
use App\Models\Office;
use Illuminate\Http\JsonResponse;

class ContactsController extends Controller
{
    /**
     * GET /api/v1/contacts — агрегат страницы «Тамос»:
     * горячие линии, головной офис и региональные представительства.
     */
    public function index(): JsonResponse
    {
        $headOffice = Office::query()->where('is_head', true)->first();

        return response()->json([
            'data' => [
                'hotlines' => HotlineResource::collection(Hotline::ordered()->get()),
                'headOffice' => $headOffice ? new OfficeResource($headOffice) : null,
                'offices' => OfficeResource::collection(
                    Office::query()->where('is_head', false)->ordered()->get()
                ),
            ],
        ]);
    }
}
