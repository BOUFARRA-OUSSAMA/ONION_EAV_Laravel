<?php

namespace App\Domain\Entities;

class AttributeValue
{
    private ?int $id;
    private Attribute $attribute;
    private string $entityType;
    private int $entityId;
    private string $value;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        Attribute $attribute,
        string $entityType,
        int $entityId,
        string $value,
        ?int $id = null
    ) {
        $this->attribute = $attribute;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->value = $value;
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): int
    {
        return $this->entityId;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Get the typed value based on attribute type
     * 
     * @return mixed
     */
    public function getTypedValue()
    {
        $type = $this->attribute->getType();

        switch ($type) {
            case Attribute::TYPE_INTEGER:
                return (int) $this->value;
            case Attribute::TYPE_DECIMAL:
                return (float) $this->value;
            case Attribute::TYPE_BOOLEAN:
                return (bool) $this->value;
            case Attribute::TYPE_DATE:
                return new \DateTimeImmutable($this->value);
            case Attribute::TYPE_DATETIME:
                return new \DateTimeImmutable($this->value);
            case Attribute::TYPE_JSON:
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }
}
