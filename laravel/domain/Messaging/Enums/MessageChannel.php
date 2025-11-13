<?php

namespace Domain\Messaging\Enums;

enum MessageChannel: string
{
    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
}
