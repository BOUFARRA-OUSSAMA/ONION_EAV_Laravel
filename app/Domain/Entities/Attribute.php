<?php

namespace App\Domain\Entities;

use InvalidArgumentException;

class Attribute
{
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_DECIMAL = 'decimal';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_DATE = 'date';
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_TEXT = 'text';
    public const TYPE_JSON = 'json';

    private ?int $id;
    private string $code;
    private string $name;
    private string $type;
    private ?string $description;
    private bool $isRequired;
    private ?EntityType $entityType;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        string $code,
        string $name,
        string $type,
        ?EntityType $entityType = null,
        bool $isRequired = false,
        ?string $description = null,
        ?int $id = null
    ) {
        $this->setCode($code);
        $this->setName($name);
        $this->setType($type);
        $this->entityType = $entityType;
        $this->isRequired = $isRequired;
        $this->description = $description;
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        if (empty(trim($code))) {
            throw new InvalidArgumentException('Attribute code cannot be empty');
        }

        // Convert to lowercase and remove spaces
        $code = strtolower(str_replace(' ', '_', trim($code)));
        $this->code = $code;
        $this->touch();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Attribute name cannot be empty');
        }
        $this->name = $name;
        $this->touch();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $validTypes = [
            self::TYPE_STRING,
            self::TYPE_INTEGER,
            self::TYPE_DECIMAL,
            self::TYPE_BOOLEAN,
            self::TYPE_DATE,
            self::TYPE_DATETIME,
            self::TYPE_TEXT,
            self::TYPE_JSON
        ];

        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException("Invalid attribute type: {$type}");
        }

        $this->type = $type;
        $this->touch();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
        $this->touch();
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setIsRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
        $this->touch();
    }

    public function getEntityType(): ?EntityType
    {
        return $this->entityType;
    }

    public function setEntityType(?EntityType $entityType): void
    {
        $this->entityType = $entityType;
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
}
