<?php

declare(strict_types=1);

namespace App\DTOs;

use Domain\Contact\Entities\Conversation;

readonly class MessageFlowInputDTO extends DataTransfer
{
    public function __construct(
        public Conversation $conversation,
        public int          $messageId,
        public string       $contactPhone,
        public string       $buttonId,
        public string       $replyAction,
        public array        $extraInfo
    ) {}
}
