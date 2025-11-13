<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use Domain\Messaging\Entities\Conversation;
use Domain\Messaging\Enums\ConversationStatus;
use Domain\Messaging\Repositories\IConversationRepository;

class ConversationRepository extends BaseRepository implements IConversationRepository
{
    protected string $entityClass = Conversation::class;

    public function hasActiveFor(int $contactId): bool {
        return $this->exists(['contact_id' => $contactId, 'status' => ConversationStatus::ACTIVE->value]);
    }
}
