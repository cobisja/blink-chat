<?php

declare(strict_types=1);

namespace App\Controller\Api\Transformer\Shared;

use JetBrains\PhpStorm\ArrayShape;

readonly class EmailAvailabilityShowTransformer
{
    #[ArrayShape([
        'available' => "bool"
    ])]
    public function transform(bool $availability): array
    {
        return [
            'available' => $availability
        ];
    }
}
