<?php

declare(strict_types=1);

namespace Messaging\Domain\Enum;

enum MessageChannel: string
{
    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
}
