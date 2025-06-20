<?php

declare(strict_types=1);

namespace Domain\Contact\Entities;

class MessageButton
{
    public function __construct(
        private string $id,
        private string $label,
        private ?string $type = 'reply'
    ) {}

    public static function create(array $data): self
    {
        return new self(
            id: $data['id'],
            label: $data['label'],
            type: $data['type'] ?? 'reply'
        );
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            id: $data['id'],
            label: $data['label'],
            type: $data['type'] ?? 'reply'
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'label' => $this->label,
            'type'  => $this->type,
        ];
    }
}
