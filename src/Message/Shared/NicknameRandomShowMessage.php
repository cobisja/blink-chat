<?php

declare(strict_types=1);

namespace App\Message\Shared;

final readonly class NicknameRandomShowMessage
{
    public function __construct(public ?string $baseName)
    {
    }
}