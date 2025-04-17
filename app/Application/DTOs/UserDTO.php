<?php

namespace App\Application\DTOs;

class UserDTO
{
    private string $name;
    private string $email;
    private ?string $phone;
    private string $status;
    private ?int $id;

    public function __construct(
        string $name,
        string $email,
        ?string $phone = null,
        string $status = 'pending',
        ?int $id = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->status = $status;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Create a DTO from array data
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['status'] ?? 'pending',
            $data['id'] ?? null
        );
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
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
        ];
    }
}
