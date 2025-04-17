<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Attribute as AttributeEntity;
use App\Domain\Entities\EntityType as EntityTypeEntity;
use App\Domain\Interfaces\Repositories\AttributeRepositoryInterface;
use App\Infrastructure\Persistence\Models\Attribute as AttributeModel;
use App\Infrastructure\Persistence\Models\EntityType as EntityTypeModel;

class EloquentAttributeRepository implements AttributeRepositoryInterface
{
    /**
     * Find an attribute by ID
     *
     * @param int $id
     * @return AttributeEntity|null
     */
    public function findById(int $id): ?AttributeEntity
    {
        $attributeModel = AttributeModel::with('entityType')->find($id);

        if (!$attributeModel) {
            return null;
        }

        return $this->mapToDomainEntity($attributeModel);
    }

    /**
     * Find an attribute by code
     *
     * @param string $code
     * @return AttributeEntity|null
     */
    public function findByCode(string $code): ?AttributeEntity
    {
        $attributeModel = AttributeModel::with('entityType')->where('code', $code)->first();

        if (!$attributeModel) {
            return null;
        }

        return $this->mapToDomainEntity($attributeModel);
    }

    /**
     * Find attributes by entity type
     *
     * @param EntityTypeEntity $entityType
     * @return array
     */
    public function findByEntityType(EntityTypeEntity $entityType): array
    {
        $attributeModels = AttributeModel::with('entityType')
            ->where('entity_type_id', $entityType->getId())
            ->get();

        return $attributeModels->map([$this, 'mapToDomainEntity'])->toArray();
    }

    /**
     * Get all attributes
     *
     * @return array
     */
    public function findAll(): array
    {
        $attributeModels = AttributeModel::with('entityType')->get();

        return $attributeModels->map([$this, 'mapToDomainEntity'])->toArray();
    }

    /**
     * Save an attribute (create or update)
     *
     * @param AttributeEntity $attribute
     * @return AttributeEntity
     */
    public function save(AttributeEntity $attribute): AttributeEntity
    {
        $attributes = [
            'code' => $attribute->getCode(),
            'name' => $attribute->getName(),
            'type' => $attribute->getType(),
            'description' => $attribute->getDescription(),
            'is_required' => $attribute->isRequired(),
            'entity_type_id' => $attribute->getEntityType() ? $attribute->getEntityType()->getId() : null,
        ];

        if ($attribute->getId()) {
            $attributeModel = AttributeModel::find($attribute->getId());

            if (!$attributeModel) {
                throw new \RuntimeException('Attribute not found');
            }

            $attributeModel->update($attributes);
        } else {
            $attributeModel = AttributeModel::create($attributes);
        }

        // Reload with relation
        $attributeModel = AttributeModel::with('entityType')->find($attributeModel->id);

        return $this->mapToDomainEntity($attributeModel);
    }

    /**
     * Delete an attribute
     *
     * @param AttributeEntity $attribute
     * @return bool
     */
    public function delete(AttributeEntity $attribute): bool
    {
        if (!$attribute->getId()) {
            throw new \RuntimeException('Cannot delete attribute without ID');
        }

        $attributeModel = AttributeModel::find($attribute->getId());

        if (!$attributeModel) {
            return false;
        }

        return $attributeModel->delete();
    }

    /**
     * Map an attribute model to a domain entity
     *
     * @param AttributeModel $attributeModel
     * @return AttributeEntity
     */
    private function mapToDomainEntity(AttributeModel $attributeModel): AttributeEntity
    {
        $entityType = null;

        if ($attributeModel->entityType) {
            $entityType = new EntityTypeEntity(
                $attributeModel->entityType->code,
                $attributeModel->entityType->name,
                $attributeModel->entityType->description,
                $attributeModel->entityType->id
            );
        }

        return new AttributeEntity(
            $attributeModel->code,
            $attributeModel->name,
            $attributeModel->type,
            $entityType,
            $attributeModel->is_required,
            $attributeModel->description,
            $attributeModel->id
        );
    }
}
