<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Save a user (create or update)
     *
     * @param User $user
     * @return User
     */
    public function save(User $user): User;

    /**
     * Delete a user
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;

    /**
     * Find users by criteria
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findByCriteria(array $criteria, int $page = 1, int $perPage = 15): array;
}
