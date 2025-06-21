<?php

declare(strict_types=1);

namespace Domain\Contact\Repositories;

use App\Repositories\IRepository;
use Domain\Contact\Entities\MessageButton;

/**
 * @extends IRepository<MessageButton>
 */
interface IMessageButtonRepository extends IRepository
{
}

