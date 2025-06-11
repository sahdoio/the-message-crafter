<?php

namespace App\Domain\Contact\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case TEMPLATE = 'template';
}
