<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\UserDTO;
use App\Application\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get a list of users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $criteria = [];

        if ($request->has('status')) {
            $criteria['status'] = $request->input('status');
        }

        if ($request->has('name')) {
            $criteria['name'] = $request->input('name');
        }

        if ($request->has('email')) {
            $criteria['email'] = $request->input('email');
        }

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);

        $result = $this->userService->findUsers($criteria, $page, $perPage);

        return $this->successResponse($result);
    }

    /**
     * Get a specific user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        return $this->successResponse($user->toArray());
    }

    /**
     * Create a new user
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $userDTO = UserDTO::fromArray($request->validated());
            $createdUser = $this->userService->createUser($userDTO);

            return $this->successResponse($createdUser->toArray(), 'User created successfully', 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while creating the user');
        }
    }

    /**
     * Update a user
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $userData = $request->validated();
            $userData['id'] = $id;

            $userDTO = UserDTO::fromArray($userData);
            $updatedUser = $this->userService->updateUser($id, $userDTO);

            return $this->successResponse($updatedUser->toArray(), 'User updated successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while updating the user');
        }
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->userService->deleteUser($id);

            if ($result) {
                return $this->successResponse(null, 'User deleted successfully');
            }

            return $this->errorResponse('Failed to delete user');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the user');
        }
    }

    /**
     * Assign roles to a user
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function assignRoles(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        try {
            $this->userService->assignRoles($id, $request->input('roles'));

            return $this->successResponse(null, 'Roles assigned successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while assigning roles: ' . $e->getMessage());
        }
    }

    /**
     * Remove a role from a user
     *
     * @param int $userId
     * @param int $roleId
     * @return JsonResponse
     */
    public function removeRole(int $userId, int $roleId): JsonResponse
    {
        $user = $this->userService->getUserById($userId);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        try {
            $this->userService->removeRole($userId, $roleId);

            return $this->successResponse(null, 'Role removed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while removing the role: ' . $e->getMessage());
        }
    }
}
