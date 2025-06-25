<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Actions\Contact\Strategies\DiveDeeperStrategy;
use App\Actions\Contact\Strategies\HelpOrSupportStrategy;
use App\Actions\Contact\Strategies\StartCourseStrategy;
use App\Actions\Contact\Strategies\IMessageFlow;
use Domain\Contact\Enums\ReplyAction;

readonly class FlowStrategyResolver
{
    public function __construct() {}

    public function resolve(string $replyAction): IMessageFlow
    {
        return match ($replyAction) {
            ReplyAction::START_COURSE->value => new StartCourseStrategy(),
            ReplyAction::DIVE_DEEPER->value => new DiveDeeperStrategy(),
            ReplyAction::HELP_OR_SUPPORT->value => new HelpOrSupportStrategy(),
            default => throw new \InvalidArgumentException("Unsupported reply action: $replyAction"),
        };
    }
}
