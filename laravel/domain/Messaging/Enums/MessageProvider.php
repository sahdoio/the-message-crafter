<?php

namespace Domain\Messaging\Enums;

enum MessageProvider: string
{
    case SYSTEM = 'system';
    case AI = 'ai';
}
