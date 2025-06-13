<?php

declare(strict_types=1);

namespace App\DTOs;

use ArrayObject;

readonly class UserApiLoginOutputDTO extends DataTransfer
{
    public function __construct(
        public string $token,
        public ArrayObject $user
    ) {}
}
