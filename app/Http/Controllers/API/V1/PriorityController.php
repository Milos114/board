<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PriorityRequest;
use App\Http\Resources\V1\PriorityResource;
use App\Models\Priority;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Priorities
 *
 * API endpoints for managing priorities.
 */
class PriorityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $priorities = Priority::get();

        return PriorityResource::collection($priorities);
    }

    public function store(): JsonResponse
    {
        $priority = Priority::create(request()->all());

        return response()->json($priority, Response::HTTP_CREATED);
    }

    public function update(Priority $priority, PriorityRequest $request): JsonResponse
    {
        $priority->update($request->validated());

        return response()->json($priority, Response::HTTP_OK);
    }

    public function show(Priority $priority): PriorityResource
    {
        return new PriorityResource($priority);
    }

    public function destroy(Priority $priority): JsonResponse
    {
        $priority->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
