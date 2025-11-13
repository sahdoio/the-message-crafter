<?php

declare(strict_types=1);

namespace Shared\Domain\Enum;

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

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
}
