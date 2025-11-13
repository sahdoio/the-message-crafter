<?php

declare(strict_types=1);

namespace Domain\Messaging\Repositories;

use App\Repositories\IRepository;
use Domain\Messaging\Entities\Conversation;

/**
 * @extends IRepository<Conversation>
 */
interface IConversationRepository extends IRepository
{
    public function hasActiveFor(int $contactId): bool;
}

