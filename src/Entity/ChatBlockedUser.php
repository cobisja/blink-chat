<?php

namespace App\Entity;

use App\Repository\ChatBlockedUserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ChatBlockedUserRepository::class)]
#[ORM\Table(name: '`chats_blocked_users`')]
class ChatBlockedUser
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'blockedUsers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $blockerUser = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $blockedUser = null;

    #[ORM\Column]
    private ?DateTimeImmutable $blockedAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getBlockerUser(): ?User
    {
        return $this->blockerUser;
    }

    public function setBlockerUser(?User $blockerUser): static
    {
        $this->blockerUser = $blockerUser;

        return $this;
    }

    public function getBlockedUser(): ?User
    {
        return $this->blockedUser;
    }

    public function setBlockedUser(?User $blockedUser): static
    {
        $this->blockedUser = $blockedUser;

        return $this;
    }

    public function getBlockedAt(): ?DateTimeImmutable
    {
        return $this->blockedAt;
    }

    public function setBlockedAt(DateTimeImmutable $blockedAt): static
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }
}
