<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $this->auth->register(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'token' => $data['token'],
            'user' => new UserResource($data['user']),
        ], 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $data = $this->auth->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'token' => $data['token'],
            'user' => new UserResource($data['user']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Logged out.']);
    }

    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
