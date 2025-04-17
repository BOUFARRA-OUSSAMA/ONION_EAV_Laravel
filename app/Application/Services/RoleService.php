<?php

namespace App\Application\Services;

use App\Application\DTOs\RoleDTO;
use App\Domain\Entities\Role;
use App\Domain\Interfaces\Repositories\RoleRepositoryInterface;
use App\Infrastructure\Persistence\Models\Role as RoleModel;
use InvalidArgumentException;

class RoleService
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get a role by ID
     *
     * @param int $id
     * @return RoleDTO|null
     */
    public function getRoleById(int $id): ?RoleDTO
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return null;
        }

        return new RoleDTO(
            $role->getName(),
            $role->getCode(),
            $role->getDescription(),
            $role->getId()
        );
    }

    /**
     * Get a role by code
     *
     * @param string $code
     * @return RoleDTO|null
     */
    public function getRoleByCode(string $code): ?RoleDTO
    {
        $role = $this->roleRepository->findByCode($code);

        if (!$role) {
            return null;
        }

        return new RoleDTO(
            $role->getName(),
            $role->getCode(),
            $role->getDescription(),
            $role->getId()
        );
    }

    /**
     * Create a new role
     *
     * @param RoleDTO $roleDTO
     * @return RoleDTO
     */
    public function createRole(RoleDTO $roleDTO): RoleDTO
    {
        // Check if code already exists
        $existingRole = $this->roleRepository->findByCode($roleDTO->getCode());
        if ($existingRole) {
            throw new InvalidArgumentException('Role code already exists');
        }

        // Create the role entity
        $role = new Role(
            $roleDTO->getName(),
            $roleDTO->getCode(),
            $roleDTO->getDescription()
        );

        // Save the role
        $savedRole = $this->roleRepository->save($role);

        // Return DTO with the generated ID
        return new RoleDTO(
            $savedRole->getName(),
            $savedRole->getCode(),
            $savedRole->getDescription(),
            $savedRole->getId()
        );
    }

    /**
     * Update an existing role
     *
     * @param RoleDTO $roleDTO
     * @return RoleDTO
     */
    public function updateRole(RoleDTO $roleDTO): RoleDTO
    {
        if (!$roleDTO->getId()) {
            throw new InvalidArgumentException('Role ID is required for update');
        }

        // Find the role
        $role = $this->roleRepository->findById($roleDTO->getId());
        if (!$role) {
            throw new InvalidArgumentException('Role not found');
        }

        // Check if code already exists (if changed)
        if ($role->getCode() !== $roleDTO->getCode()) {
            $existingRole = $this->roleRepository->findByCode($roleDTO->getCode());
            if ($existingRole && $existingRole->getId() !== $roleDTO->getId()) {
                throw new InvalidArgumentException('Role code already exists');
            }
        }

        // Update the role entity
        $role->setName($roleDTO->getName());
        $role->setCode($roleDTO->getCode());
        $role->setDescription($roleDTO->getDescription());

        // Save the role
        $savedRole = $this->roleRepository->save($role);

        // Return updated DTO
        return new RoleDTO(
            $savedRole->getName(),
            $savedRole->getCode(),
            $savedRole->getDescription(),
            $savedRole->getId()
        );
    }

    /**
     * Delete a role
     *
     * @param int $id
     * @return bool
     */
    public function deleteRole(int $id): bool
    {
        $role = $this->roleRepository->findById($id);
        if (!$role) {
            throw new InvalidArgumentException('Role not found');
        }

        return $this->roleRepository->delete($role);
    }

    /**
     * Find roles by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findRoles(array $criteria = []): array
    {
        $roles = $this->roleRepository->findByCriteria($criteria);

        // Convert domain entities to DTOs
        return array_map(function (Role $role) {
            return new RoleDTO(
                $role->getName(),
                $role->getCode(),
                $role->getDescription(),
                $role->getId()
            );
        }, $roles);
    }

    /**
     * Get all roles
     *
     * @return array
     */
    public function getAllRoles(): array
    {
        return $this->findRoles();
    }

    /**
     * Get users by role
     *
     * @param int $roleId
     * @return array
     */
    public function getUsersByRole(int $roleId): array
    {
        $roleModel = RoleModel::with('users')->find($roleId);

        if (!$roleModel) {
            throw new InvalidArgumentException('Role not found');
        }

        return $roleModel->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
            ];
        })->toArray();
    }
}
