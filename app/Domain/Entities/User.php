<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Status;
use InvalidArgumentException;

class User
{
    private ?int $id;
    private string $name;
    private string $email;
    private ?string $phone;
    private Status $status;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        string $name,
        string $email,
        ?string $phone = null,
        ?Status $status = null,
        ?int $id = null
    ) {
        $this->setName($name);
        $this->setEmail($email);
        $this->phone = $phone;
        $this->status = $status ?? new Status(Status::PENDING);
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Name cannot be empty');
        }
        $this->name = $name;
        $this->touch();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
        $this->email = $email;
        $this->touch();
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
        $this->touch();
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
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

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
