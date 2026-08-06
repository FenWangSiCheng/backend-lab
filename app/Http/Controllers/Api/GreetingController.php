<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GreetRequest;
use App\Services\GreetingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GreetingController extends Controller
{
    public function __construct(
        private readonly GreetingService $greetingService,
    ) {}

    public function __invoke(GreetRequest $request): JsonResponse
    {
        return response()->json([
            'message' => $this->greetingService->greet($request->validated('name')),
        ], Response::HTTP_CREATED);
    }
}
