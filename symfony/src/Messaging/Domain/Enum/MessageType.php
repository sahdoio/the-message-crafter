<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum MessageType: string
{
    case TEXT = 'text';
    case TEMPLATE = 'template';
    case INTERACTIVE = 'interactive';
}
