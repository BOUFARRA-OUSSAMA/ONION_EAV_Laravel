<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

    use ApiResponseTrait;
    /**
     * Handle user login and token creation
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = Auth::user();

        // Check user status
        if ($user->status !== 'active') {
            Auth::logout();
            return $this->errorResponse('Your account is not active', 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'code' => $role->code
                    ];
                }),
            ],
            'token' => $token,
        ], 'Logged in successfully');
    }

    /**
     * Handle user logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get the authenticated user
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = Auth::user();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'code' => $role->code
                ];
            }),
        ]);
    }
}
