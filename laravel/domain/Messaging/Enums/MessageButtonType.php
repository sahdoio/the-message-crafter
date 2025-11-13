<?php

declare(strict_types=1);

namespace Domain\Messaging\Enums;

enum MessageButtonType: string
{
    case TEXT = 'text';
    case URL = 'url';
}
