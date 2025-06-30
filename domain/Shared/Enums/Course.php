<?php

namespace Domain\Shared\Enums;

enum Course: string
{
    case DOMAIN_DRIVEN_DESIGN = 'Domain Driven Design';
    case EVENT_DRIVEN_ARCHITECTURE = 'Event Driven Architecture';
    case CLEAN_CODE = 'Clean Code';
    case TEST_DRIVEN_DEVELOPMENT = 'Test Driven Development';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
