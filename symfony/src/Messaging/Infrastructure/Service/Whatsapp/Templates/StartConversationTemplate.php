<?php

declare(strict_types=1);

namespace Messaging\Infrastructure\Service\Whatsapp\Templates;

use Messaging\Domain\Entity\Contact;
use Messaging\Domain\Entity\Conversation;
use Messaging\Domain\Entity\Message;
use Messaging\Domain\Entity\MessageButton;
use Messaging\Domain\Enum\MessageButtonType;
use Messaging\Domain\Repository\ContactRepositoryInterface;
use Messaging\Domain\Repository\MessageButtonRepositoryInterface;
use Messaging\Domain\ValueObject\Body\TemplateBody;
use Messaging\Domain\ValueObject\MessageBody;
use Messaging\Infrastructure\Service\Whatsapp\Builders\ContactTemplateBuilder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Uuid;

class StartConversationTemplate extends ContactTemplateBuilder
{
    public function __construct(
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly MessageButtonRepositoryInterface $messageButtonRepository,
        #[Autowire(env: 'WHATSAPP_TEMPLATE_NAME')]
        string $templateName,
        #[Autowire(env: 'WHATSAPP_MESSAGE_TYPE')]
        private readonly string $messageType,
        #[Autowire(env: 'WHATSAPP_LANGUAGE_CODE')]
        private readonly string $languageCode,
        #[Autowire(env: 'WHATSAPP_TEMPLATE_IMAGE_URL')]
        private readonly string $templateImageUrl
    ) {
        $this->templateName = $templateName;
    }

    public function build(Conversation $conversation, Message $message): MessageBody
    {
        $buttons = [
            'Start Course',
            'Dive Deeper',
            'Help or Support',
        ];

        $buttonComponents = [];
        foreach ($buttons as $index => $action) {
            $button = new MessageButton();
            $button->setButtonId(Uuid::v7()->toRfc4122());
            $button->setMessage($message);
            $button->setType(MessageButtonType::TEXT);
            $button->setAction($action);
            
            $this->messageButtonRepository->save($button);

            $buttonComponents[] = $this->generateButtonComponent($button, $index);
        }

        $contact = $this->contactRepository->find($conversation->getContact()->getId());

        if (!$contact) {
            throw new \RuntimeException('Contact not found');
        }

        return new MessageBody(
            type: $this->messageType,
            to: $contact->getPhone(),
            body: new TemplateBody(
                name: $this->templateName,
                languageCode: $this->languageCode,
                components: array_merge(
                    [
                        $this->generateHeaderComponent($this->imageUrl()),
                        $this->generateBodyComponent($contact),
                    ],
                    $buttonComponents
                )
            )
        );
    }

    public function imageUrl(): ?string
    {
        return $this->templateImageUrl;
    }
}
