<?php

namespace Domain\Messaging\Enums;

enum MessageStatus: string
{
    case SENT = 'sent';
    case FINISHED = 'finished';
}
