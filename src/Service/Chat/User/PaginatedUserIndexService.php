<?php

declare(strict_types=1);

namespace App\Service\Chat\User;

use App\Service\Paginator\PaginatorServiceInterface;

readonly class PaginatedUserIndexService
{
    public function __construct(private PaginatorServiceInterface $paginatorService)
    {
    }

    public function __invoke(?string $query, ?string $sort = null, int $page = 1, int $perPage = 10)
    {
        return $this->paginatorService->getInstance($query, $sort, $page, $perPage);
    }
}
