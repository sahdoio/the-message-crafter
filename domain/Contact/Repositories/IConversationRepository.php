<?php

declare(strict_types=1);

namespace Domain\Contact\Repositories;

use App\Repositories\IRepository;
use Domain\Contact\Entities\Conversation;

/**
 * @extends IRepository<Conversation>
 */
interface IConversationRepository extends IRepository
{
    public function hasActiveFor(int $contactId): bool;
}

