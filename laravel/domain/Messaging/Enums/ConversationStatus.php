<?php

namespace Domain\Messaging\Enums;

enum ConversationStatus: string
{
    case ACTIVE = 'active';
    case FINISHED = 'finished';
}
