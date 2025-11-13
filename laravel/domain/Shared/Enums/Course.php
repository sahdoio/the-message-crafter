<?php

namespace Domain\Shared\Enums;

enum Course: string
{
    case DOMAIN_DRIVEN_DESIGN = 'DDD';
    case EVENT_DRIVEN_ARCHITECTURE = 'EDA';
    case CLEAN_CODE = 'Clean Code';
    case TEST_DRIVEN_DEVELOPMENT = 'TDD';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
