<?php

namespace App\Domain\Contact\Enums;

enum MessageChannel: string
{
    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
}
