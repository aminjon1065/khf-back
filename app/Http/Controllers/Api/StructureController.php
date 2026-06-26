<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\LeaderResource;
use App\Http\Resources\RegionalOfficeResource;
use App\Models\Department;
use App\Models\Leader;
use App\Models\RegionalOffice;
use Illuminate\Http\JsonResponse;

class StructureController extends Controller
{
    /**
     * GET /api/v1/structure — руководство, подразделения и территориальные органы.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'leadership' => LeaderResource::collection(Leader::ordered()->get()),
                'departments' => DepartmentResource::collection(Department::ordered()->get()),
                'offices' => RegionalOfficeResource::collection(RegionalOffice::ordered()->get()),
            ],
        ]);
    }
}
