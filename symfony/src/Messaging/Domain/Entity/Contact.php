<?php

declare(strict_types=1);

namespace Messaging\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Messaging\Domain\Event\ConversationStarted;
use Messaging\Domain\Event\MessageReceived;
use Messaging\Domain\Exception\ConversationAlreadyStartedException;
use Messaging\Domain\Repository\ConversationRepositoryInterface;
use Shared\Domain\Event\HasDomainEvents;

#[ORM\Entity]
#[ORM\Table(name: 'contacts')]
class Contact
{
    use HasDomainEvents;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'boolean')]
    private bool $verified = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    private ?ConversationRepositoryInterface $conversationRepository = null;

    private function __construct(
        string $name,
        string $email,
        ?string $phone = null,
        bool $verified = false
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->verified = $verified;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public static function create(
        string $name,
        string $email,
        ?string $phone = null,
        bool $verified = false
    ): self {
        return new self($name, $email, $phone, $verified);
    }

    public function setDependencies(ConversationRepositoryInterface $conversationRepository): void
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function hasName(): bool
    {
        return trim($this->name) !== '';
    }

    public function hasActiveConversation(): bool
    {
        if (!$this->conversationRepository) {
            throw new \RuntimeException('ConversationRepository not injected');
        }

        return $this->conversationRepository->hasActiveFor($this->id);
    }

    public function startConversation(): Conversation
    {
        if ($this->hasActiveConversation()) {
            throw new ConversationAlreadyStartedException(
                'Contact already has an active conversation'
            );
        }

        if (!$this->conversationRepository) {
            throw new \RuntimeException('ConversationRepository not injected');
        }

        $conversation = Conversation::create($this->id);
        $this->conversationRepository->save($conversation);

        $this->recordDomainEvent(new ConversationStarted(
            conversationId: $conversation->getId(),
            contactId: $this->id
        ));

        return $conversation;
    }

    public function messageReceived(
        int $conversationId,
        int $messageId,
        string $replyAction,
        ?string $buttonId = null,
        array $extraInfo = []
    ): void {
        $this->recordDomainEvent(new MessageReceived(
            conversationId: $conversationId,
            messageId: $messageId,
            contactPhone: $this->phone,
            replyAction: $replyAction,
            buttonId: $buttonId,
            extraInfo: $extraInfo
        ));
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Business methods
    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->touch();
    }

    public function updateEmail(string $email): void
    {
        $this->email = $email;
        $this->touch();
    }

    public function updatePhone(?string $phone): void
    {
        $this->phone = $phone;
        $this->touch();
    }

    public function verify(): void
    {
        $this->verified = true;
        $this->touch();
    }

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
