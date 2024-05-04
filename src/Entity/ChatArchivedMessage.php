<?php

namespace App\Entity;

use App\Repository\ChatArchivedMessageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ChatArchivedMessageRepository::class)]
#[ORM\Table(name: '`chats_archived_messages`')]
class ChatArchivedMessage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'archivedMessages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ChatMessage $message = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $archivedBy = null;

    #[ORM\Column]
    private ?DateTimeImmutable $archivedAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getMessage(): ?ChatMessage
    {
        return $this->message;
    }

    public function setMessage(?ChatMessage $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getArchivedBy(): ?User
    {
        return $this->archivedBy;
    }

    public function setArchivedBy(?User $archivedBy): static
    {
        $this->archivedBy = $archivedBy;

        return $this;
    }

    public function getArchivedAt(): ?DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(DateTimeImmutable $archivedAt): static
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }
}
