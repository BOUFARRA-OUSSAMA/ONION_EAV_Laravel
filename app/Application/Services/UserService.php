<?php

namespace App\Application\Services;


use App\Application\DTOs\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\ValueObjects\Status;
use App\Infrastructure\Persistence\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get a user by ID
     *
     * @param int $id
     * @return UserDTO|null
     */
    public function getUserById(int $id): ?UserDTO
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return null;
        }

        return new UserDTO(
            $user->getName(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getStatus()->getValue(),
            $user->getId()
        );
    }

    /**
     * Get a user by email
     *
     * @param string $email
     * @return UserDTO|null
     */
    public function getUserByEmail(string $email): ?UserDTO
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        return new UserDTO(
            $user->getName(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getStatus()->getValue(),
            $user->getId()
        );
    }

    /**
     * Create a new user
     *
     * @param UserDTO $userDTO
     * @param string|null $password
     * @return UserDTO
     */
    public function createUser(UserDTO $userDTO, ?string $password = null): UserDTO
    {
        // Check if email already exists
        $existingUser = $this->userRepository->findByEmail($userDTO->getEmail());
        if ($existingUser) {
            throw new InvalidArgumentException('Email already exists');
        }

        // Create the user entity
        $status = new Status($userDTO->getStatus() ?: 'pending');
        $user = new User(
            $userDTO->getName(),
            $userDTO->getEmail(),
            $userDTO->getPhone(),
            $status
        );

        // For password, we need to interact directly with the Eloquent model
        // since we don't handle passwords in our domain entities
        $savedUser = $this->userRepository->save($user);

        if ($password) {
            $userModel = UserModel::find($savedUser->getId());
            $userModel->password = Hash::make($password);
            $userModel->save();
        }

        // Return DTO with the generated ID
        return new UserDTO(
            $savedUser->getName(),
            $savedUser->getEmail(),
            $savedUser->getPhone(),
            $savedUser->getStatus()->getValue(),
            $savedUser->getId()
        );
    }

    /**
     * Update an existing user
     *
     * @param int $id
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function updateUser(int $id, UserDTO $userDTO): UserDTO
    {
        // Find the user
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        // Check if email already exists (if changed)
        if ($user->getEmail() !== $userDTO->getEmail()) {
            $existingUser = $this->userRepository->findByEmail($userDTO->getEmail());
            if ($existingUser && $existingUser->getId() !== $id) {
                throw new InvalidArgumentException('Email already exists');
            }
        }

        // Update the user entity
        $user->setName($userDTO->getName());
        $user->setEmail($userDTO->getEmail());
        $user->setPhone($userDTO->getPhone());

        if ($userDTO->getStatus()) {
            $user->setStatus(new Status($userDTO->getStatus()));
        }

        // Save the user
        $savedUser = $this->userRepository->save($user);

        // Return updated DTO
        return new UserDTO(
            $savedUser->getName(),
            $savedUser->getEmail(),
            $savedUser->getPhone(),
            $savedUser->getStatus()->getValue(),
            $savedUser->getId()
        );
    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        return $this->userRepository->delete($user);
    }

    /**
     * Find users by criteria
     *
     * @param array $criteria
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findUsers(array $criteria, int $page = 1, int $perPage = 15): array
    {
        $result = $this->userRepository->findByCriteria($criteria, $page, $perPage);

        // Convert domain entities to DTOs
        $dtos = array_map(function (User $user) {
            return new UserDTO(
                $user->getName(),
                $user->getEmail(),
                $user->getPhone(),
                $user->getStatus()->getValue(),
                $user->getId()
            );
        }, $result['items']);

        return [
            'items' => $dtos,
            'total' => $result['total'],
            'current_page' => $result['current_page'],
            'per_page' => $result['per_page'],
            'last_page' => $result['last_page'],
        ];
    }

    /**
     * Change user status
     *
     * @param int $id
     * @param string $status
     * @return UserDTO
     */
    public function changeUserStatus(int $id, string $status): UserDTO
    {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        $user->setStatus(new Status($status));
        $savedUser = $this->userRepository->save($user);

        return new UserDTO(
            $savedUser->getName(),
            $savedUser->getEmail(),
            $savedUser->getPhone(),
            $savedUser->getStatus()->getValue(),
            $savedUser->getId()
        );
    }

    /**
     * Assign roles to a user
     *
     * @param int $userId
     * @param array $roleIds
     * @return bool
     */
    public function assignRoles(int $userId, array $roleIds): bool
    {
        $userModel = UserModel::find($userId);

        if (!$userModel) {
            throw new InvalidArgumentException('User not found');
        }

        $userModel->roles()->sync($roleIds, false);

        return true;
    }

    /**
     * Remove a role from a user
     *
     * @param int $userId
     * @param int $roleId
     * @return bool
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        $userModel = UserModel::find($userId);

        if (!$userModel) {
            throw new InvalidArgumentException('User not found');
        }

        $userModel->roles()->detach($roleId);

        return true;
    }

    /**
     * Get user roles
     *
     * @param int $userId
     * @return array
     */
    public function getUserRoles(int $userId): array
    {
        $userModel = UserModel::with('roles')->find($userId);

        if (!$userModel) {
            throw new InvalidArgumentException('User not found');
        }

        return $userModel->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'code' => $role->code,
                'description' => $role->description,
            ];
        })->toArray();
    }
}
