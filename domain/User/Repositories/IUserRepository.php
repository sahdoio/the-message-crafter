<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use App\Repositories\IRepository;
use Domain\User\Entities\User;

/**
 * @extends IRepository<User>
 */
interface IUserRepository extends IRepository
{
    function createUserToken(int $userId): string;
    function deleteOldTokens(int $userId): bool;
}
