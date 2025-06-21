<?php

declare(strict_types=1);

namespace Domain\User\Entities;

class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $password,
        public ?string $rememberToken = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
