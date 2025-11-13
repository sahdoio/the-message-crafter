<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum MessageButtonType: string
{
    case TEXT = 'text';
    case REPLY = 'reply';
}
