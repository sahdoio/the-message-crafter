<?php

namespace Domain\Contact\Enums;

enum MessageStatus: string
{
    case SENT = 'sent';
    case FINISHED = 'finished';
}
