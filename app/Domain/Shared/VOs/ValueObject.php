<?php

declare(strict_types=1);

namespace App\Domain\Shared\VOs;

abstract class ValueObject
{
    public function values(): array
    {
        return $this->deepToArray(get_object_vars($this));
    }

    private function deepToArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($value instanceof self) {
                $data[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $data[$key] = $this->deepToArray($value);
            }
        }

        return $data;
    }
}
