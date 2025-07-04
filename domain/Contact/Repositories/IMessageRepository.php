<?php

declare(strict_types=1);

namespace Domain\Contact\Repositories;

use App\Repositories\IRepository;
use Domain\Contact\Entities\Message;

/**
 * @extends IRepository<Message>
 */
interface IMessageRepository extends IRepository {}
