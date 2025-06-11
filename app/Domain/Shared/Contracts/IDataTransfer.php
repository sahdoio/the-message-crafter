<?php

declare(strict_types=1);

namespace App\Domain\Shared\Contracts;

interface IDataTransfer
{
    function values(): array;

    function get(string $property);
}
