<?php

namespace App\Domain\Entities;

use InvalidArgumentException;

class EntityType
{
    public const USER = 'user';
    public const DOCTOR = 'doctor';
    public const PATIENT = 'patient';
    public const CHATBOT = 'chatbot';

    private ?int $id;
    private string $code;
    private string $name;
    private ?string $description;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        string $code,
        string $name,
        ?string $description = null,
        ?int $id = null
    ) {
        $this->setCode($code);
        $this->setName($name);
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
            throw new InvalidArgumentException('Entity type code cannot be empty');
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
            throw new InvalidArgumentException('Entity type name cannot be empty');
        }
        $this->name = $name;
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
