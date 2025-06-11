<?php

declare(strict_types=1);

namespace App\Domain\User\DTOs;

use App\Domain\Shared\DTOs\DataTransfer;

readonly class UserApiLoginInputDTO extends DataTransfer
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
