<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\Contact\Entities\Conversation;
use Domain\Contact\Enums\ConversationStatus;
use Domain\Contact\Repositories\IConversationRepository;

class ConversationRepository extends BaseRepository implements IConversationRepository
{
    protected string $entityClass = Conversation::class;

    public function hasActiveFor(int $contactId): bool {
        return $this->exist(['contact_id' => $contactId, 'status' => ConversationStatus::ACTIVE->value]);
    }
}
