<?php

namespace App\Service\Paginator;

interface PaginatorServiceInterface
{
    public function getInstance(?string $query, ?string $sort = null, int $page = 1, int $perPage = 10);

    public function getCurrentPageResults(): iterable;

    public function getNumberOfResults(): int;

    public function hasNextPage(): bool;

    public function hasPreviousPage(): bool;

    public function getNextPage(): int;

    public function getPreviousPage(): int;
}