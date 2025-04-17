<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Role;

interface RoleRepositoryInterface
{
    /**
     * Find role by ID
     *
     * @param int $id
     * @return Role|null
     */
    public function findById(int $id): ?Role;

    /**
     * Find role by code
     *
     * @param string $code
     * @return Role|null
     */
    public function findByCode(string $code): ?Role;

    /**
     * Find roles by criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findByCriteria(array $criteria): array;

    /**
     * Save role
     *
     * @param Role $role
     * @return Role
     */
    public function save(Role $role): Role;

    /**
     * Delete role
     *
     * @param Role $role
     * @return bool
     */
    public function delete(Role $role): bool;
}
