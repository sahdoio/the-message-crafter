<?php

namespace Domain\Contact\Enums;

enum MessageChannel: string
{
    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
}
