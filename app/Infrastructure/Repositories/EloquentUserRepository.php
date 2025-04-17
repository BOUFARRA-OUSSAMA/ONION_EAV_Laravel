<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User as UserEntity;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\ValueObjects\Status;
use App\Infrastructure\Persistence\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID
     *
     * @param int $id
     * @return UserEntity|null
     */
    public function findById(int $id): ?UserEntity
    {
        $userModel = UserModel::find($id);

        if (!$userModel) {
            return null;
        }

        return $this->mapToDomainEntity($userModel);
    }

    /**
     * Find a user by email
     *
     * @param string $email
     * @return UserEntity|null
     */
    public function findByEmail(string $email): ?UserEntity
    {
        $userModel = UserModel::where('email', $email)->first();

        if (!$userModel) {
            return null;
        }

        return $this->mapToDomainEntity($userModel);
    }

    /**
     * Save a user (create or update)
     *
     * @param UserEntity $user
     * @return UserEntity
     */
    public function save(UserEntity $user): UserEntity
    {
        $attributes = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'status' => $user->getStatus()->getValue(),
        ];

        if ($user->getId()) {
            $userModel = UserModel::find($user->getId());

            if (!$userModel) {
                throw new \RuntimeException('User not found');
            }

            $userModel->update($attributes);
        } else {
            $userModel = UserModel::create($attributes);
        }

        return $this->mapToDomainEntity($userModel);
    }

    /**
     * Delete a user
     *
     * @param UserEntity $user
     * @return bool
     */
    public function delete(UserEntity $user): bool
    {
        if (!$user->getId()) {
            throw new \RuntimeException('Cannot delete user without ID');
        }

        $userModel = UserModel::find($user->getId());

        if (!$userModel) {
            return false;
        }

        return $userModel->delete();
    }

    /**
     * Find users by criteria
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findByCriteria(array $criteria, int $page = 1, int $perPage = 15): array
    {
        $query = UserModel::query();

        // Apply criteria
        foreach ($criteria as $field => $value) {
            if ($field === 'status') {
                $query->where('status', $value);
            } elseif ($field === 'name') {
                $query->where('name', 'like', "%{$value}%");
            } elseif ($field === 'email') {
                $query->where('email', 'like', "%{$value}%");
            }
        }

        // Paginate
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Map to domain entities
        $users = $paginator->items();
        $mappedUsers = array_map([$this, 'mapToDomainEntity'], $users);

        return [
            'items' => $mappedUsers,
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    /**
     * Map a user model to a domain entity
     *
     * @param UserModel $userModel
     * @return UserEntity
     */
    private function mapToDomainEntity(UserModel $userModel): UserEntity
    {
        $status = new Status($userModel->status);

        return new UserEntity(
            $userModel->name,
            $userModel->email,
            $userModel->phone,
            $status,
            $userModel->id
        );
    }
}
