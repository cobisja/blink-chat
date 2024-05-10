<?php

declare(strict_types=1);

namespace App\Message\Chat\User;

final readonly class PaginatedUserIndexMessage
{
    public function __construct(
        public ?string $query,
        public ?string $sort = null,
        public ?int $page = null,
        public ?int $perPage = null
    ) {
    }
}