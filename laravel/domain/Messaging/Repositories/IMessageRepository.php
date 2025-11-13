<?php

declare(strict_types=1);

namespace Domain\Messaging\Repositories;

use App\Repositories\IRepository;
use Domain\Messaging\Entities\Message;

/**
 * @extends IRepository<Message>
 */
interface IMessageRepository extends IRepository {}
