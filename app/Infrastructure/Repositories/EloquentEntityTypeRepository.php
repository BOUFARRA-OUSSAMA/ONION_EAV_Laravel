<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\EntityType as EntityTypeEntity;
use App\Domain\Interfaces\Repositories\EntityTypeRepositoryInterface;
use App\Infrastructure\Persistence\Models\EntityType as EntityTypeModel;

class EloquentEntityTypeRepository implements EntityTypeRepositoryInterface
{
    /**
     * Find an entity type by ID
     *
     * @param int $id
     * @return EntityTypeEntity|null
     */
    public function findById(int $id): ?EntityTypeEntity
    {
        $entityTypeModel = EntityTypeModel::find($id);

        if (!$entityTypeModel) {
            return null;
        }

        return $this->mapToDomainEntity($entityTypeModel);
    }

    /**
     * Find an entity type by code
     *
     * @param string $code
     * @return EntityTypeEntity|null
     */
    public function findByCode(string $code): ?EntityTypeEntity
    {
        $entityTypeModel = EntityTypeModel::where('code', $code)->first();

        if (!$entityTypeModel) {
            return null;
        }

        return $this->mapToDomainEntity($entityTypeModel);
    }

    /**
     * Get all entity types
     *
     * @return array
     */
    public function findAll(): array
    {
        $entityTypeModels = EntityTypeModel::all();

        return $entityTypeModels->map([$this, 'mapToDomainEntity'])->toArray();
    }

    /**
     * Save an entity type (create or update)
     *
     * @param EntityTypeEntity $entityType
     * @return EntityTypeEntity
     */
    public function save(EntityTypeEntity $entityType): EntityTypeEntity
    {
        $attributes = [
            'code' => $entityType->getCode(),
            'name' => $entityType->getName(),
            'description' => $entityType->getDescription(),
        ];

        if ($entityType->getId()) {
            $entityTypeModel = EntityTypeModel::find($entityType->getId());

            if (!$entityTypeModel) {
                throw new \RuntimeException('Entity type not found');
            }

            $entityTypeModel->update($attributes);
        } else {
            $entityTypeModel = EntityTypeModel::create($attributes);
        }

        return $this->mapToDomainEntity($entityTypeModel);
    }

    /**
     * Delete an entity type
     *
     * @param EntityTypeEntity $entityType
     * @return bool
     */
    public function delete(EntityTypeEntity $entityType): bool
    {
        if (!$entityType->getId()) {
            throw new \RuntimeException('Cannot delete entity type without ID');
        }

        $entityTypeModel = EntityTypeModel::find($entityType->getId());

        if (!$entityTypeModel) {
            return false;
        }

        return $entityTypeModel->delete();
    }

    /**
     * Map an entity type model to a domain entity
     *
     * @param EntityTypeModel $entityTypeModel
     * @return EntityTypeEntity
     */
    private function mapToDomainEntity(EntityTypeModel $entityTypeModel): EntityTypeEntity
    {
        return new EntityTypeEntity(
            $entityTypeModel->code,
            $entityTypeModel->name,
            $entityTypeModel->description,
            $entityTypeModel->id
        );
    }
}
