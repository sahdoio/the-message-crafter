<?php

declare(strict_types=1);

namespace Domain\Messaging\Repositories;

use App\Repositories\IRepository;
use Domain\Messaging\Entities\MessageButton;

/**
 * @extends IRepository<MessageButton>
 */
interface IMessageButtonRepository extends IRepository
{
}

