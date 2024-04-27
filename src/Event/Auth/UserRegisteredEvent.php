<?php

declare(strict_types=1);

namespace App\Event\Auth;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(private readonly User $user)
    {
    }

    public function payload(): array
    {
        return [
            'user' => $this->user,
            'occurred_on' => new DateTimeImmutable()
        ];
    }
}