<?php

namespace Domain\Contact\Enums;

enum ConversationStatus: string
{
    case ACTIVE = 'active';
    case FINISHED = 'finished';
}
