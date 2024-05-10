<?php

namespace App\Tests\Service\Chat\User;

use App\Service\Chat\User\PaginatedUserIndexService;
use App\Service\Paginator\PaginatorServiceInterface;
use PHPUnit\Framework\TestCase;

class PaginatedUserIndexServiceTest extends TestCase
{
    private PaginatorServiceInterface $paginatorService;

    protected function setUp(): void
    {
        $this->paginatorService = $this->createMock(PaginatorServiceInterface::class);
    }

    /**
     * @test
     */
    public function it_should_return_a_paginator_service_instance(): void
    {
        $this
            ->paginatorService
            ->method('getInstance')
            ->willReturnSelf();

        $paginator = (new PaginatedUserIndexService($this->paginatorService))(
            query: 'test'
        );

        $this->assertInstanceOf(PaginatorServiceInterface::class, $paginator);
    }
}
