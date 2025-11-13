<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum ReplyAction: string
{
    case START_COURSE = 'Start Course';
    case DIVE_DEEPER = 'Dive Deeper';
    case HELP_OR_SUPPORT = 'Help or Support';
}
