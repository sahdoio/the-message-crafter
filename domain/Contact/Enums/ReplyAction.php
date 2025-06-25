<?php

namespace Domain\Contact\Enums;

enum ReplyAction: string
{
    case START_COURSE = 'start_course';
    case DIVE_DEEPER = 'dive_deeper';
    case HELP_OR_SUPPORT = 'help_or_support';
}
