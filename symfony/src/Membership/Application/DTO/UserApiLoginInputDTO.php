<?php

declare(strict_types=1);

namespace Membership\Application\DTO;

final readonly class UserApiLoginInputDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
