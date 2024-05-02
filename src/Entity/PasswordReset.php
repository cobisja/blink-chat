<?php

namespace App\Entity;

use App\Exception\Auth\ResetTokenCannotBeCreatedException;
use App\Repository\PasswordResetRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PasswordResetRepository::class)]
#[ORM\Table(name: '`passwords_resets`')]
class PasswordReset
{
    final public const CODE_LENGTH = 48;
    final public const CODE_TTL = '+15 minutes';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\Column]
    private ?DateTimeImmutable $validUntil = null;

    /**
     * @throws ResetTokenCannotBeCreatedException
     */
    public function __construct()
    {
        $this->createToken();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getValidUntil(): ?DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function setValidUntil(DateTimeImmutable $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @throws ResetTokenCannotBeCreatedException
     */
    public function createToken(): void
    {
        try {
            $this->token = sha1(random_bytes(self::CODE_LENGTH));
            $this->validUntil = new DateTimeImmutable(self::CODE_TTL);
        } catch (RandomException) {
            throw new ResetTokenCannotBeCreatedException();
        }
    }

    public function isExpired(): bool
    {
        return $this->validUntil < new DateTimeImmutable();
    }
}
