<?php

declare(strict_types=1);

namespace Domain\Shared\Contracts;

interface IDataTransfer
{
    function values(): array;

    function get(string $property);
}
