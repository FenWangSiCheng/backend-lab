<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return UserResource::collection(
            $this->userService->paginate($perPage),
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(int $user): UserResource
    {
        return new UserResource($this->userService->find($user));
    }

    public function update(UpdateUserRequest $request, int $user): UserResource
    {
        return new UserResource(
            $this->userService->update($user, $request->validated()),
        );
    }

    public function destroy(int $user): Response
    {
        $this->userService->delete($user);

        return response()->noContent();
    }
}
