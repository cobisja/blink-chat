<?php

declare(strict_types=1);

namespace App\Event\Auth;

use DateTimeImmutable;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(private readonly string $userId)
    {
    }

    #[ArrayShape([
        'user_id' => "string",
        'occurred_on' => "\DateTimeImmutable"
    ])]
    public function payload(): array
    {
        return [
            'user_id' => $this->userId,
            'occurred_on' => new DateTimeImmutable()
        ];
    }
}