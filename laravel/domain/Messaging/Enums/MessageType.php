<?php

namespace Domain\Messaging\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case TEMPLATE = 'template';
}
