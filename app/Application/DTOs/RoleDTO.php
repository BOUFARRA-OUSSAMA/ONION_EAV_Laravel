<?php

namespace App\Application\DTOs;

class RoleDTO
{
    private string $name;
    private string $code;
    private ?string $description;
    private ?int $id;

    public function __construct(
        string $name,
        string $code,
        ?string $description = null,
        ?int $id = null
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
        ];
    }

    /**
     * Create from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['code'],
            $data['description'] ?? null,
            $data['id'] ?? null
        );
    }
}
