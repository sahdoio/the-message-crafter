<?php

declare(strict_types=1);

namespace App\Domain\Contact\Support;

use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\Entities\MessageButton;
use App\Domain\Contact\Enums\MessageButtonType;
use App\Domain\Contact\VOs\MessageBody;
use App\Domain\Contact\VOs\TemplateBody;
use App\Domain\Shared\Exceptions\ResourceNotFoundException;
use App\Facades\Repository;
use ArrayObject;
use Ramsey\Uuid\Uuid;

readonly class WhatsappTemplateBuilder
{
    public function __construct(private ?string $templateName = null) {}

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
            $button = Repository::for(MessageButton::class)->create([
                'button_id' => Uuid::uuid7()->toString(),
                'message_id' => $message->id,
                'type' => MessageButtonType::TEXT->value,
                'action' => $action
            ]);

            $buttonComponents[] = $this->generateButtonComponent($button, $index);
        }

        $contact = Repository::for(Contact::class)->findById($message->contact_id);

        if (!$contact) {
            throw new ResourceNotFoundException('Contact not found');
        }

        return new MessageBody(
            type: 'template',
            to: $contact->phone,
            template: new TemplateBody(
                name: $this->templateName,
                languageCode: 'pt_BR',
                components: array_merge([
                    $this->generateHeaderComponent($imageUrl),
                    $this->generateBodyComponent($contact),
                ], $buttonComponents)
            )
        );
    }

    private function generateHeaderComponent(string $imageUrl): array
    {
        return [
            "type" => "header",
            "parameters" => [
                [
                    "type" => "image",
                    "image" => [
                        "link" => $imageUrl,
                    ],
                ],
            ],
        ];
    }

    private function generateBodyComponent(ArrayObject $contact): array
    {
        return [
            "type" => "body",
            "parameters" => [
                ["type" => "text", "text" => $contact->name],
            ],
        ];
    }

    private function generateButtonComponent(ArrayObject $button, int $index): array
    {
        return [
            "type" => "button",
            "sub_type" => "quick_reply",
            "index" => $index,
            "parameters" => [
                [
                    'type' => 'payload',
                    'payload' => $button->button_id,
                ],
            ],
        ];
    }
}
