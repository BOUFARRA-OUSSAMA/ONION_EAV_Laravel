<?php

namespace App\Domain\Traits;

use App\Domain\Entities\Attribute;

trait HasAttributes
{
    /**
     * Get an attribute value
     *
     * @param string $attributeCode
     * @return mixed|null
     */
    public function getAttribute(string $attributeCode)
    {
        // This method will be implemented in the repository
        // For now, we define the contract that will be used

        // When this method is used in an entity, the repository will:
        // 1. Find the attribute by code
        // 2. Find the attribute value for this entity and attribute
        // 3. Return the typed value

        return null;
    }

    /**
     * Set an attribute value
     *
     * @param string $attributeCode
     * @param mixed $value
     * @return void
     */
    public function setAttribute(string $attributeCode, $value): void
    {
        // This method will be implemented in the repository
        // For now, we define the contract that will be used

        // When this method is used in an entity, the repository will:
        // 1. Find the attribute by code
        // 2. Create or update the attribute value for this entity and attribute
    }

    /**
     * Check if an attribute exists
     *
     * @param string $attributeCode
     * @return bool
     */
    public function hasAttribute(string $attributeCode): bool
    {
        // This method will be implemented in the repository
        return false;
    }

    /**
     * Get all attributes for this entity
     *
     * @return array
     */
    public function getAttributes(): array
    {
        // This method will be implemented in the repository
        return [];
    }

    /**
     * Get the entity type code
     * 
     * @return string
     */
    abstract public function getEntityTypeCode(): string;
}
