<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\Contact\Entities\Message;
use Domain\Contact\Repositories\IMessageRepository;

class MessageRepository extends BaseRepository implements IMessageRepository
{
    protected string $entityClass = Message::class;
}
