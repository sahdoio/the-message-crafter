<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\Messaging\Entities\Message;
use Domain\Messaging\Repositories\IMessageRepository;

class MessageRepository extends BaseRepository implements IMessageRepository
{
    protected string $entityClass = Message::class;
}
