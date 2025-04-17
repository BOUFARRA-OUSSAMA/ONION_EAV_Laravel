<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Attribute;
use App\Domain\Entities\AttributeValue;

interface AttributeValueRepositoryInterface
{
    /**
     * Find an attribute value by ID
     *
     * @param int $id
     * @return AttributeValue|null
     */
    public function findById(int $id): ?AttributeValue;

    /**
     * Find attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return array
     */
    public function findByEntity(string $entityType, int $entityId): array;

    /**
     * Find a specific attribute value for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $attributeId
     * @return AttributeValue|null
     */
    public function findByEntityAndAttribute(string $entityType, int $entityId, int $attributeId): ?AttributeValue;

    /**
     * Save an attribute value (create or update)
     *
     * @param AttributeValue $attributeValue
     * @return AttributeValue
     */
    public function save(AttributeValue $attributeValue): AttributeValue;

    /**
     * Delete an attribute value
     *
     * @param AttributeValue $attributeValue
     * @return bool
     */
    public function delete(AttributeValue $attributeValue): bool;

    /**
     * Delete all attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return bool
     */
    public function deleteByEntity(string $entityType, int $entityId): bool;
}
