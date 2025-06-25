<?php

declare(strict_types=1);

namespace App\Actions\Contact\Strategies;

use App\DTOs\MessageFlowInputDTO;
use App\Exceptions\ResourceNotFoundException;
use App\Facades\Messenger;
use App\Facades\Repository;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TextBody;
use Illuminate\Support\Facades\Log;

class LegacyStartCourseStrategy implements IMessageFlow
{
    /**
     * @throws ResourceNotFoundException
     */
    function handle(MessageFlowInputDTO $data): void
    {
        Log::info('DefaultFlowStrategy', ['errors' => 'Button reply action not found']);

        if (isset($data->message->button_buy)) {
            Log::error('DefaultFlowStrategy', ['errors' => 'Button reply action already exists']);
            return;
        }

        $repo = Repository::for(MessageButton::class);
        /**
         * @var MessageButton $button
         */
        $button = $repo->findOne(['button_id' => $data->buttonId]);

        if (!$button) {
            throw new ResourceNotFoundException('Button not found');
        }

        $repo->update($button->id, [
            'is_clicked' => true,
            'active' => $data->replyAction
        ]);

        switch ($data->replyAction) {
            case __('sale_express.messages.first.actions.yes'):
                $this->handleYesAnswer($data);
                break;
            default:
                Log::error('DefaultFlowStrategy', ['errors' => 'Button reply action not found']);
                break;
        }
    }

    private function handleYesAnswer(MessageFlowInputDTO $data): void
    {
        Messenger::send(new MessageBody(
            type: 'text',
            to: '5511970954944',
            text: new TextBody(body: __('sale_express.messages.no_answer.whatsapp_body')),
        ));
    }
}
