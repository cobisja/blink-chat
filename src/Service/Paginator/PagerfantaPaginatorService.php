<?php

declare(strict_types=1);

namespace App\Service\Paginator;

use App\Repository\UserRepository;
use Pagerfanta\Adapter\EmptyAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Exception\LessThan1CurrentPageException;
use Pagerfanta\Exception\LessThan1MaxPerPageException;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;

class PagerfantaPaginatorService implements PaginatorServiceInterface
{
    private Pagerfanta $paginatorInstance;

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function getInstance(?string $query, ?string $sort = null, int $page = 1, int $perPage = 10): static
    {
        try {
            $this->paginatorInstance = Pagerfanta::createForCurrentPageWithMaxPerPage(
                adapter: new QueryAdapter(
                    $this->userRepository->findBySearchQuery(
                        query: $query,
                        sort: $sort,
                        returnQueryBuilder: true
                    ),
                ),
                currentPage: $page,
                maxPerPage: $perPage
            );
        } catch (LessThan1CurrentPageException|LessThan1MaxPerPageException|OutOfRangeCurrentPageException) {
            $this->paginatorInstance = new Pagerfanta(new EmptyAdapter());
        }

        return $this;
    }

    public function getCurrentPageResults(): iterable
    {
        return $this->paginatorInstance->getCurrentPageResults();
    }

    public function getNumberOfResults(): int
    {
        return $this->paginatorInstance->getNbResults();
    }

    public function hasNextPage(): bool
    {
        return $this->paginatorInstance->hasNextPage();
    }

    public function hasPreviousPage(): bool
    {
        return $this->paginatorInstance->hasPreviousPage();
    }

    public function getNextPage(): int
    {
        return $this->paginatorInstance->getNextPage();
    }

    public function getPreviousPage(): int
    {
        return $this->paginatorInstance->getPreviousPage();
    }
}
