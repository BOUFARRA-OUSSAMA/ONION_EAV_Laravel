<?php

namespace App\Application\Services;

use App\Domain\Entities\Attribute;
use App\Domain\Entities\AttributeValue;
use App\Domain\Entities\EntityType;
use App\Domain\Interfaces\Repositories\AttributeRepositoryInterface;
use App\Domain\Interfaces\Repositories\AttributeValueRepositoryInterface;
use App\Domain\Interfaces\Repositories\EntityTypeRepositoryInterface;
use InvalidArgumentException;

class EAVService
{
    private EntityTypeRepositoryInterface $entityTypeRepository;
    private AttributeRepositoryInterface $attributeRepository;
    private AttributeValueRepositoryInterface $attributeValueRepository;

    public function __construct(
        EntityTypeRepositoryInterface $entityTypeRepository,
        AttributeRepositoryInterface $attributeRepository,
        AttributeValueRepositoryInterface $attributeValueRepository
    ) {
        $this->entityTypeRepository = $entityTypeRepository;
        $this->attributeRepository = $attributeRepository;
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * Set an attribute value for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @param string $attributeCode
     * @param mixed $value
     * @return void
     */
    public function setAttributeValue(
        string $entityType,
        int $entityId,
        string $attributeCode,
        $value
    ): void {
        // Find the attribute
        $attribute = $this->attributeRepository->findByCode($attributeCode);
        if (!$attribute) {
            throw new InvalidArgumentException("Attribute '{$attributeCode}' not found");
        }

        // Check if the attribute is applicable to this entity type
        if ($attribute->getEntityType() && $attribute->getEntityType()->getCode() !== $entityType) {
            throw new InvalidArgumentException(
                "Attribute '{$attributeCode}' is not applicable to entity type '{$entityType}'"
            );
        }

        // Convert the value to string (for storage)
        $stringValue = $this->convertValueToString($value, $attribute->getType());

        // Create attribute value entity
        $attributeValue = new AttributeValue(
            $attribute,
            $entityType,
            $entityId,
            $stringValue
        );

        // Save the attribute value
        $this->attributeValueRepository->save($attributeValue);
    }

    /**
     * Get an attribute value for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @param string $attributeCode
     * @return mixed|null
     */
    public function getAttributeValue(
        string $entityType,
        int $entityId,
        string $attributeCode
    ) {
        // Find the attribute
        $attribute = $this->attributeRepository->findByCode($attributeCode);
        if (!$attribute) {
            return null;
        }

        // Find the attribute value
        $attributeValue = $this->attributeValueRepository->findByEntityAndAttribute(
            $entityType,
            $entityId,
            $attribute->getId()
        );

        if (!$attributeValue) {
            return null;
        }

        // Return the typed value
        return $attributeValue->getTypedValue();
    }

    /**
     * Get all attribute values for an entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return array
     */
    public function getAttributeValues(string $entityType, int $entityId): array
    {
        $attributeValues = $this->attributeValueRepository->findByEntity($entityType, $entityId);

        $result = [];
        foreach ($attributeValues as $attributeValue) {
            $result[$attributeValue->getAttribute()->getCode()] = $attributeValue->getTypedValue();
        }

        return $result;
    }

    /**
     * Convert a value to string for storage
     *
     * @param mixed $value
     * @param string $type
     * @return string
     */
    private function convertValueToString($value, string $type): string
    {
        return match ($type) {
            Attribute::TYPE_INTEGER, Attribute::TYPE_DECIMAL, Attribute::TYPE_STRING, Attribute::TYPE_TEXT => (string)$value,
            Attribute::TYPE_BOOLEAN => $value ? '1' : '0',
            Attribute::TYPE_DATE, Attribute::TYPE_DATETIME => $value instanceof \DateTimeInterface
                ? $value->format('Y-m-d H:i:s')
                : (string)$value,
            Attribute::TYPE_JSON => json_encode($value),
            default => (string)$value,
        };
    }
}
