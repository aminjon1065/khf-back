<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DirectionResource;
use App\Http\Resources\ProgramResource;
use App\Models\Direction;
use App\Models\Program;
use Illuminate\Http\JsonResponse;

class ActivitiesController extends Controller
{
    /**
     * GET /api/v1/activities — направления деятельности и программы.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'directions' => DirectionResource::collection(Direction::ordered()->get()),
                'programs' => ProgramResource::collection(Program::ordered()->get()),
            ],
        ]);
    }
}
