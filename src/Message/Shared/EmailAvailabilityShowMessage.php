<?php

declare(strict_types=1);

namespace App\Message\Shared;

final readonly class EmailAvailabilityShowMessage
{
    public function __construct(public string $email)
    {
    }
}
