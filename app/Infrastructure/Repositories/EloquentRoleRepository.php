<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Role as RoleEntity;
use App\Domain\Interfaces\Repositories\RoleRepositoryInterface;
use App\Infrastructure\Persistence\Models\Role as RoleModel;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    /**
     * Find a role by ID
     *
     * @param int $id
     * @return RoleEntity|null
     */
    public function findById(int $id): ?RoleEntity
    {
        $roleModel = RoleModel::find($id);

        if (!$roleModel) {
            return null;
        }

        return $this->mapToDomainEntity($roleModel);
    }

    /**
     * Find a role by code
     *
     * @param string $code
     * @return RoleEntity|null
     */
    public function findByCode(string $code): ?RoleEntity
    {
        $roleModel = RoleModel::where('code', $code)->first();

        if (!$roleModel) {
            return null;
        }

        return $this->mapToDomainEntity($roleModel);
    }

    /**
     * Find roles by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findByCriteria(array $criteria): array
    {
        $query = RoleModel::query();
        
        if (isset($criteria['name'])) {
            $query->where('name', 'like', '%' . $criteria['name'] . '%');
        }
        
        if (isset($criteria['code'])) {
            $query->where('code', 'like', '%' . $criteria['code'] . '%');
        }
        
        $roleModels = $query->get();
        
        return $roleModels->map(function ($roleModel) {
            return $this->mapToDomainEntity($roleModel);
        })->toArray();
    }

    /**
     * Save a role (create or update)
     *
     * @param RoleEntity $role
     * @return RoleEntity
     */
    public function save(RoleEntity $role): RoleEntity
    {
        $attributes = [
            'name' => $role->getName(),
            'code' => $role->getCode(),
            'description' => $role->getDescription()
        ];
        
        if ($role->getId()) {
            $roleModel = RoleModel::find($role->getId());
            $roleModel->update($attributes);
        } else {
            $roleModel = RoleModel::create($attributes);
        }
        
        return $this->mapToDomainEntity($roleModel);
    }

    /**
     * Delete a role
     *
     * @param RoleEntity $role
     * @return bool
     */
    public function delete(RoleEntity $role): bool
    {
        $roleModel = RoleModel::find($role->getId());
        
        if (!$roleModel) {
            return false;
        }
        
        return $roleModel->delete();
    }

    /**
     * Map a Role model to a domain entity
     *
     * @param RoleModel $roleModel
     * @return RoleEntity
     */
    private function mapToDomainEntity(RoleModel $roleModel): RoleEntity
    {
        $role = new RoleEntity(
            $roleModel->name,
            $roleModel->code,
            $roleModel->description
        );
        
        // Set the ID after creation
        $role->setId($roleModel->id);
        
        return $role;
    }
}