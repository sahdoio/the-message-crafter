<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

abstract class ValueObject
{
    abstract public function values(): array;

    public function equals(ValueObject $other): bool
    {
        return $this->values() === $other->values();
    }

    protected function deepToArray(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn($item) => $this->deepToArray($item), $value);
        }

        if ($value instanceof ValueObject) {
            return $value->values();
        }

        return $value;
    }
}
