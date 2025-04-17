<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Attribute;
use App\Domain\Entities\EntityType;

interface AttributeRepositoryInterface
{
    /**
     * Find an attribute by ID
     *
     * @param int $id
     * @return Attribute|null
     */
    public function findById(int $id): ?Attribute;

    /**
     * Find an attribute by code
     *
     * @param string $code
     * @return Attribute|null
     */
    public function findByCode(string $code): ?Attribute;

    /**
     * Find attributes by entity type
     *
     * @param EntityType $entityType
     * @return array
     */
    public function findByEntityType(EntityType $entityType): array;

    /**
     * Get all attributes
     *
     * @return array
     */
    public function findAll(): array;

    /**
     * Save an attribute (create or update)
     *
     * @param Attribute $attribute
     * @return Attribute
     */
    public function save(Attribute $attribute): Attribute;

    /**
     * Delete an attribute
     *
     * @param Attribute $attribute
     * @return bool
     */
    public function delete(Attribute $attribute): bool;
}
