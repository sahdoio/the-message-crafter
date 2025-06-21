<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use Domain\Contact\Entities\Contact;
use Domain\Contact\Entities\Message;
use Domain\Contact\Entities\MessageButton;
use Domain\Contact\Enums\MessageButtonType;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TemplateBody;
use Ramsey\Uuid\Uuid;

class WhatsappTemplateBuilder
{
    public function __construct(
        protected ?string $templateName = null,
    )
    {
        if (is_null($this->templateName)) {
            $this->templateName = config('whatsapp.template_name');
        }
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function build(Message $message): MessageBody
    {
        $imageUrl = config('whatsapp.template_image_url');

        $buttons = [
            'Solicitar Empréstimo',
            'Já Solicitei',
            'Liquidação de Contrato',
            'Saque-Aniversário FGTS',
            'Cancelar Empréstimo',
            'Cópia do Contrato',
            'Tirar Dúvidas',
        ];

        $buttonComponents = [];
        foreach (array_slice($buttons, 0, 3) as $index => $action) {
            $button = Repository::for(MessageButton::class)->create([
                'button_id' => Uuid::uuid7()->toString(),
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $action,
            ]);

            $buttonComponents[] = $this->generateButtonComponent($button, $index);
        }

        $contact = Repository::for(Contact::class)->findById($message->contactId);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        return new MessageBody(
            type: 'template',
            to: $contact->phone,
            template: new TemplateBody(
                name: $this->templateName,
                languageCode: 'pt_BR',
                components: array_merge(
                    [
                        $this->generateHeaderComponent($imageUrl),
                        $this->generateBodyComponent($contact),
                    ],
                    $buttonComponents
                )
            )
        );
    }

    private function generateHeaderComponent(string $imageUrl): array
    {
        return [
            'type' => 'header',
            'parameters' => [[
                'type' => 'image',
                'image' => ['link' => $imageUrl],
            ]],
        ];
    }

    private function generateBodyComponent(Contact $contact): array
    {
        return [
            'type' => 'body',
            'parameters' => [[
                'type' => 'text',
                'text' => $contact->name,
            ]],
        ];
    }

    private function generateButtonComponent(MessageButton $button, int $index): array
    {
        return [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => $index,
            'parameters' => [[
                'type' => 'payload',
                'payload' => $button->buttonId,
            ]],
        ];
    }
}
