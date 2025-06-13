<?php

declare(strict_types=1);

namespace App\DTOs;

use Domain\Shared\Contracts\IDataTransfer;
use InvalidArgumentException;
use JsonSerializable;

readonly class DataTransfer implements IDataTransfer, JsonSerializable
{
    /**
     * Returns all public and protected properties of the DTO.
     */
    public function values(): array
    {
        return get_object_vars($this);
    }

    /**
     * Gets the value of a given property, using a getter method if available.
     *
     * @throws InvalidArgumentException
     */
    public function get(string $property): mixed
    {
        $getter = 'get' . ucfirst($property);

        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }

        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException(sprintf(
                "The property '%s' doesn't exist in '%s' DTO class",
                $property,
                $this::class
            ));
        }

        return $this->{$property};
    }

    /**
     * Returns an array representation of the DTO for JSON serialization.
     */
    public function jsonSerialize(): array
    {
        return $this->values();
    }

    /**
     * Magic getter for accessing property values.
     *
     * @throws InvalidArgumentException
     */
    public function __get(string $property): mixed
    {
        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException(sprintf(
                "The property '%s' doesn't exist in '%s' DTO class",
                $property,
                $this::class
            ));
        }

        return $this->{$property};
    }

    /**
     * Magic setter is disabled to enforce immutability.
     *
     * @throws InvalidArgumentException
     */
    public function __set(string $name, mixed $value): void
    {
        throw new InvalidArgumentException(
            sprintf("The property '%s' is read-only", $name)
        );
    }

    /**
     * Checks if a property exists on the DTO.
     */
    public function __isset(string $name): bool
    {
        return property_exists($this, $name);
    }
}
