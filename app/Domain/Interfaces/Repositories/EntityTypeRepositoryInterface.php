<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\EntityType;

interface EntityTypeRepositoryInterface
{
    /**
     * Find an entity type by ID
     *
     * @param int $id
     * @return EntityType|null
     */
    public function findById(int $id): ?EntityType;

    /**
     * Find an entity type by code
     *
     * @param string $code
     * @return EntityType|null
     */
    public function findByCode(string $code): ?EntityType;

    /**
     * Get all entity types
     *
     * @return array
     */
    public function findAll(): array;

    /**
     * Save an entity type (create or update)
     *
     * @param EntityType $entityType
     * @return EntityType
     */
    public function save(EntityType $entityType): EntityType;

    /**
     * Delete an entity type
     *
     * @param EntityType $entityType
     * @return bool
     */
    public function delete(EntityType $entityType): bool;
}
