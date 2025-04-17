<?php

namespace App\Domain\Entities;

class Role
{
    private ?int $id = null;
    private string $name;
    private string $code;
    private ?string $description;

    public function __construct(string $name, string $code, ?string $description = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
