<?php

declare(strict_types=1);

namespace App\Actions\Messaging;

use App\Actions\Messaging\Strategies\DiveDeeperStrategy;
use App\Actions\Messaging\Strategies\HelpOrSupportStrategy;
use App\Actions\Messaging\Strategies\IMessageFlow;
use App\Actions\Messaging\Strategies\StartCourseStrategy;
use Domain\Messaging\Enums\ReplyAction;

readonly class FlowStrategyResolver
{
    public function __construct() {}

    public function resolve(string $replyAction): IMessageFlow
    {
        return match ($replyAction) {
            ReplyAction::START_COURSE->value => app(StartCourseStrategy::class),
            ReplyAction::DIVE_DEEPER->value => app(DiveDeeperStrategy::class),
            ReplyAction::HELP_OR_SUPPORT->value => app(HelpOrSupportStrategy::class),
            default => throw new \InvalidArgumentException("Unsupported reply action: $replyAction"),
        };
    }
}
