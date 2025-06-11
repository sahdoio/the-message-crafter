<?php

declare(strict_types=1);

namespace App\Domain\Contact\UseCases;

use App\Domain\Contact\Entities\Message;
use App\Domain\Contact\Contracts\IMessageFlow;
use App\Domain\Contact\UseCases\Strategies\AIFlowStrategy;
use App\Domain\Contact\UseCases\Strategies\DefaultFlowStrategy;
use App\Facades\Repository;

readonly class FlowStrategyResolver
{
    public function __construct() {}

    public function resolve(int $messageId): IMessageFlow
    {
        $message = Repository::setEntity(Message::class)->findOne(['id' => $messageId]);

        // TODO - use enum
        if ($message->provider === 'ai') {
            return new AIFlowStrategy();
        }

        return new DefaultFlowStrategy();
    }
}
