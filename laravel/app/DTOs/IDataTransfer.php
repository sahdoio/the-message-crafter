<?php

declare(strict_types=1);

namespace App\DTOs;

interface IDataTransfer
{
    function values(): array;

    function get(string $property);
}
