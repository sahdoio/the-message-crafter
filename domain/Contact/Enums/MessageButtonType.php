<?php

declare(strict_types=1);

namespace Domain\Contact\Enums;

enum MessageButtonType: string
{
    case TEXT = 'text';
    case URL = 'url';
}
