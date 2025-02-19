<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UserStoreRequest;
use App\Http\Requests\V1\UserUpdateRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Users
 *
 * API endpoints for managing users.
 */
class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::filter();

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        return UserResource::make($user);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        $user->update($request->validated());

        return response()->json($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}
