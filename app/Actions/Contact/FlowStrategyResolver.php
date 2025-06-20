<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Facades\Repository;
use App\Models\Message;
use App\Actions\Contact\Strategies\AIFlowStrategy;
use App\Actions\Contact\Strategies\DefaultFlowStrategy;
use Domain\Contact\Contracts\IMessageFlow;

readonly class FlowStrategyResolver
{
    public function __construct() {}

    public function resolve(Object $message): IMessageFlow
    {
        // TODO - use enum
        if ($message->provider === 'credit_card') {
            return new AIFlowStrategy();
        }

        return new DefaultFlowStrategy();
    }
}
