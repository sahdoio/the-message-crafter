<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum MessageProvider: string
{
    case SYSTEM = 'system';
    case AI = 'ai';
}
