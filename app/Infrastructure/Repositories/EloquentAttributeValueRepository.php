<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Attribute as AttributeEntity;
use App\Domain\Entities\AttributeValue as AttributeValueEntity;
use App\Domain\Interfaces\Repositories\AttributeValueRepositoryInterface;
use App\Infrastructure\Persistence\Models\Attribute as AttributeModel;
use App\Infrastructure\Persistence\Models\AttributeValue as AttributeValueModel;

class EloquentAttributeValueRepository implements AttributeValueRepositoryInterface
{
    /**
     * Find an attribute value by ID
     *
     * @param int $id
     * @return AttributeValueEntity|null
     */
    public function findById(int $id): ?AttributeValueEntity
    {
        $attributeValueModel = AttributeValueModel::with('attribute')->find($id);

        if (!$attributeValueModel) {
            return null;
        }

        return $this->mapToDomainEntity($attributeValueModel);
    }

    /**
     * Find attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return array
     */
    public function findByEntity(string $entityType, int $entityId): array
    {
        $attributeValueModels = AttributeValueModel::with('attribute')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->get();

        return $attributeValueModels->map([$this, 'mapToDomainEntity'])->toArray();
    }

    /**
     * Find a specific attribute value for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $attributeId
     * @return AttributeValueEntity|null
     */
    public function findByEntityAndAttribute(string $entityType, int $entityId, int $attributeId): ?AttributeValueEntity
    {
        $attributeValueModel = AttributeValueModel::with('attribute')
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('attribute_id', $attributeId)
            ->first();

        if (!$attributeValueModel) {
            return null;
        }

        return $this->mapToDomainEntity($attributeValueModel);
    }

    /**
     * Save an attribute value (create or update)
     *
     * @param AttributeValueEntity $attributeValue
     * @return AttributeValueEntity
     */
    public function save(AttributeValueEntity $attributeValue): AttributeValueEntity
    {
        // Find the attribute model
        $attributeModel = AttributeModel::find($attributeValue->getAttribute()->getId());

        if (!$attributeModel) {
            throw new \RuntimeException('Attribute not found');
        }

        $attributes = [
            'attribute_id' => $attributeValue->getAttribute()->getId(),
            'entity_type' => $attributeValue->getEntityType(),
            'entity_id' => $attributeValue->getEntityId(),
            'value' => $attributeValue->getValue(),
        ];

        if ($attributeValue->getId()) {
            $attributeValueModel = AttributeValueModel::find($attributeValue->getId());

            if (!$attributeValueModel) {
                throw new \RuntimeException('Attribute value not found');
            }

            $attributeValueModel->update($attributes);
        } else {
            // Try to find existing record to update
            $attributeValueModel = AttributeValueModel::where([
                'attribute_id' => $attributeValue->getAttribute()->getId(),
                'entity_type' => $attributeValue->getEntityType(),
                'entity_id' => $attributeValue->getEntityId(),
            ])->first();

            if ($attributeValueModel) {
                $attributeValueModel->update(['value' => $attributeValue->getValue()]);
            } else {
                $attributeValueModel = AttributeValueModel::create($attributes);
            }
        }

        // Reload with relation
        $attributeValueModel = AttributeValueModel::with('attribute')->find($attributeValueModel->id);

        return $this->mapToDomainEntity($attributeValueModel);
    }

    /**
     * Delete an attribute value
     *
     * @param AttributeValueEntity $attributeValue
     * @return bool
     */
    public function delete(AttributeValueEntity $attributeValue): bool
    {
        if (!$attributeValue->getId()) {
            throw new \RuntimeException('Cannot delete attribute value without ID');
        }

        $attributeValueModel = AttributeValueModel::find($attributeValue->getId());

        if (!$attributeValueModel) {
            return false;
        }

        return $attributeValueModel->delete();
    }

    /**
     * Delete all attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return bool
     */
    public function deleteByEntity(string $entityType, int $entityId): bool
    {
        return AttributeValueModel::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->delete();
    }

    /**
     * Map an attribute value model to a domain entity
     *
     * @param AttributeValueModel $attributeValueModel
     * @return AttributeValueEntity
     */
    private function mapToDomainEntity(AttributeValueModel $attributeValueModel): AttributeValueEntity
    {
        // Map the attribute
        $attributeEntity = new AttributeEntity(
            $attributeValueModel->attribute->code,
            $attributeValueModel->attribute->name,
            $attributeValueModel->attribute->type,
            null, // EntityType would need to be loaded here if needed
            $attributeValueModel->attribute->is_required,
            $attributeValueModel->attribute->description,
            $attributeValueModel->attribute->id
        );

        return new AttributeValueEntity(
            $attributeEntity,
            $attributeValueModel->entity_type,
            $attributeValueModel->entity_id,
            $attributeValueModel->value,
            $attributeValueModel->id
        );
    }
}
