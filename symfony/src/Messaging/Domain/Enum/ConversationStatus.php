<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum ConversationStatus: string
{
    case ACTIVE = 'active';
    case FINISHED = 'finished';
}
