<?php

declare(strict_types=1);

namespace App\Support;

use App\Exceptions\ResourceNotFoundException;
use ArrayObject;
use Domain\Contact\Enums\MessageButtonType;
use Domain\Contact\Repositories\IContactRepository;
use Domain\Contact\Repositories\IMessageButtonRepository;
use Domain\Contact\ValueObjects\MessageBody;
use Domain\Contact\ValueObjects\TemplateBody;
use Ramsey\Uuid\Uuid;

class WhatsappTemplateBuilder
{
    protected IContactRepository $contactRepository;
    protected IMessageButtonRepository $buttonRepository;

    public function __construct(
        protected ?string $templateName = null,
    )
    {
        $this->contactRepository = app(IContactRepository::class);
        $this->buttonRepository = app(IMessageButtonRepository::class);

        if (is_null($this->templateName)) {
            $this->templateName = config('whatsapp.template_name');
        }
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function build(ArrayObject $message): MessageBody
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
            $button = $this->buttonRepository->create([
                'button_id' => Uuid::uuid7()->toString(),
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $action,
            ]);

            $buttonComponents[] = $this->generateButtonComponent($button, $index);
        }

        $contact = $this->contactRepository->findById($message->contact_id);

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

    private function generateBodyComponent(ArrayObject $contact): array
    {
        return [
            'type' => 'body',
            'parameters' => [[
                'type' => 'text',
                'text' => $contact->name,
            ]],
        ];
    }

    private function generateButtonComponent(ArrayObject $button, int $index): array
    {
        return [
            'type' => 'button',
            'sub_type' => 'quick_reply',
            'index' => $index,
            'parameters' => [[
                'type' => 'payload',
                'payload' => $button->button_id,
            ]],
        ];
    }
}
