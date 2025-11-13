<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class UserApiLoginInputDTO extends DataTransfer
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
