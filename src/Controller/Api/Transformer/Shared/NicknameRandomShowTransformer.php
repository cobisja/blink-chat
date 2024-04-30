<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer\Shared;

readonly class NicknameRandomShowTransformer
{
    public function transform(string $nickname): array
    {
        return compact('nickname');
    }
}