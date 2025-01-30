<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LaneStoreRequest;
use App\Http\Requests\V1\LaneUpdateRequest;
use App\Http\Resources\V1\LaneResource;
use App\Models\Lane;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Lanes
 *
 * API endpoints for managing lanes.
 */
class LaneController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $lanes = Cache::remember('lanes', 60, static function () {
            return Lane::filter()->get();
        });

        return LaneResource::collection($lanes);
    }

    public function store(LaneStoreRequest $request): JsonResponse
    {
        $lane = Lane::create($request->all());

        return response()->json($lane, Response::HTTP_CREATED);
    }

    public function show(Lane $lane): LaneResource
    {
        return LaneResource::make($lane);
    }

    public function update(LaneUpdateRequest $request, Lane $lane): JsonResponse
    {
        $lane->update($request->all());

        return response()->json($lane);
    }

    public function destroy(Lane $lane): JsonResponse
    {
        $lane->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
