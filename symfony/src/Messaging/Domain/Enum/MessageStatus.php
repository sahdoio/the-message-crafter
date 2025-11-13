<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum MessageStatus: string
{
    case SENT = 'sent';
    case FINISHED = 'finished';
}
