<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use App\BaseRepositories\IRepository;

interface IUserRepository extends IRepository
{
    function createUserToken(int $userId): string;
    function deleteOldTokens(int $userId): bool;
}
