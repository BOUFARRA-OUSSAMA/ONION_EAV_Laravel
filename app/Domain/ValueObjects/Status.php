<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Status
{
    public const ACTIVE = 'active';
    public const SUSPENDED = 'suspended';
    public const PENDING = 'pending';

    private string $value;

    /**
     * @param string $value
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        if (!in_array($value, [self::ACTIVE, self::SUSPENDED, self::PENDING])) {
            throw new InvalidArgumentException("Invalid status value: {$value}");
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->value === self::SUSPENDED;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function __toString()
    {
        return $this->value;
    }
}