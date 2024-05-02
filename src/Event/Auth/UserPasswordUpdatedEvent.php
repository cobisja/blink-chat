<?php

declare(strict_types=1);

namespace App\Event\Auth;

use DateTimeImmutable;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordUpdatedEvent extends Event
{
    public function __construct(private readonly string $email)
    {
    }

    #[ArrayShape([
        'email' => "string",
        'occurred_on' => "\DateTimeImmutable"
    ])]
    public function payload(): array
    {
        return [
            'email' => $this->email,
            'occurred_on' => new DateTimeImmutable()
        ];
    }
}
