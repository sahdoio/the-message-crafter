<?php

declare(strict_types=1);

namespace App\DTOs;

use Domain\User\Entities\User;

readonly class UserApiLoginOutputDTO extends DataTransfer
{
    public function __construct(
        public string $token,
        public User $user
    ) {}
}
