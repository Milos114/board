<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaneStoreRequest;
use App\Http\Resources\StateResource;
use App\Models\Lane;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        $states = Lane::filter()->get();

        return StateResource::collection($states);
    }

    public function store(LaneStoreRequest $request): JsonResponse
    {
        $lane = Lane::create($request->all());

        return response()->json($lane, Response::HTTP_CREATED);
    }

    public function show(Lane $lane): StateResource
    {
        return StateResource::make($lane);
    }

    public function update(LaneStoreRequest $request, Lane $lane): JsonResponse
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
