<?php

declare(strict_types=1);

namespace Membership\Application\DTO;

use Membership\Domain\Entity\User;

final readonly class UserApiLoginOutputDTO
{
    public function __construct(
        public string $token,
        public User $user
    ) {
    }
}
