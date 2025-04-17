<?php

namespace App\Http\Controllers\Api;

use App\Application\DTOs\RoleDTO;
use App\Application\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponseTrait;

    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Get a list of roles
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $criteria = [];

        if ($request->has('name')) {
            $criteria['name'] = $request->input('name');
        }

        if ($request->has('code')) {
            $criteria['code'] = $request->input('code');
        }

        $roles = $this->roleService->findRoles($criteria);

        return $this->successResponse($roles);
    }

    /**
     * Get a specific role
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $role = $this->roleService->getRoleById($id);

        if (!$role) {
            return $this->errorResponse('Role not found', 404);
        }

        return $this->successResponse($role->toArray());
    }

    /**
     * Create a new role
     *
     * @param CreateRoleRequest $request
     * @return JsonResponse
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $roleDTO = new RoleDTO(
                $data['name'],
                $data['code'],
                $data['description'] ?? null
            );

            $createdRole = $this->roleService->createRole($roleDTO);

            return $this->successResponse($createdRole->toArray(), 'Role created successfully', 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while creating the role: ' . $e->getMessage());
        }
    }

    /**
     * Update a role
     *
     * @param UpdateRoleRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            $data = $request->validated();
            
            // Use RoleDTO getter methods to provide fallback values
            $roleDTO = new RoleDTO(
                $data['name'] ?? $role->getName(),
                $data['code'] ?? $role->getCode(),
                $data['description'] ?? $role->getDescription(),
                $id
            );

            $updatedRole = $this->roleService->updateRole($roleDTO);

            return $this->successResponse($updatedRole->toArray(), 'Role updated successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while updating the role: ' . $e->getMessage());
        }
    }

    /**
     * Delete a role
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            $this->roleService->deleteRole($id);

            return $this->successResponse(null, 'Role deleted successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the role: ' . $e->getMessage());
        }
    }

    /**
     * Get users with a specific role
     *
     * @param int $id
     * @return JsonResponse
     */
    public function users(int $id): JsonResponse
    {
        try {
            $role = $this->roleService->getRoleById($id);

            if (!$role) {
                return $this->errorResponse('Role not found', 404);
            }

            $users = $this->roleService->getUsersByRole($id);

            return $this->successResponse($users);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while fetching users: ' . $e->getMessage());
        }
    }
}