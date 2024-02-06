<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request\Pagination;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Request\Pagination\CursorBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedCursorBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedPageBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\OffsetBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PageBasedPagination;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PaginationFactory;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;

class PaginationFactoryTest extends TestCase
{
    #[Test]
    public function createFixedPageBasedPagination(): void
    {
        $paginationFactory = $this->createPaginationFactoryFromRequestQueryParams(
            [
                'page' => ['number' => 1],
            ],
        );

        $pagination = $paginationFactory->createFixedPageBasedPagination();

        self::assertEquals(new FixedPageBasedPagination(1), $pagination);
    }

    #[Test]
    public function createPageBasedPagination(): void
    {
        $paginationFactory = $this->createPaginationFactoryFromRequestQueryParams(
            [
                'page' => ['number' => 1, 'size' => 10],
            ],
        );

        $pagination = $paginationFactory->createPageBasedPagination();

        self::assertEquals(new PageBasedPagination(1, 10), $pagination);
    }

    #[Test]
    public function createOffsetBasedPagination(): void
    {
        $paginationFactory = $this->createPaginationFactoryFromRequestQueryParams(
            [
                'page' => ['offset' => 1, 'limit' => 10],
            ],
        );

        $pagination = $paginationFactory->createOffsetBasedPagination();

        self::assertEquals(new OffsetBasedPagination(1, 10), $pagination);
    }

    #[Test]
    public function createCursorBasedPagination(): void
    {
        $paginationFactory = $this->createPaginationFactoryFromRequestQueryParams(
            [
                'page' => ['cursor' => 'abc', 'size' => 10],
            ],
        );

        $pagination = $paginationFactory->createCursorBasedPagination();

        self::assertEquals(new CursorBasedPagination('abc', 10), $pagination);
    }

    #[Test]
    public function createFixedCursorBasedPagination(): void
    {
        $paginationFactory = $this->createPaginationFactoryFromRequestQueryParams(
            [
                'page' => ['cursor' => 'abc'],
            ],
        );

        $pagination = $paginationFactory->createFixedCursorBasedPagination();

        self::assertEquals(new FixedCursorBasedPagination('abc'), $pagination);
    }

    private function createPaginationFactoryFromRequestQueryParams(array $queryParams): PaginationFactory
    {
        return new PaginationFactory($this->createRequestWithQueryParams($queryParams));
    }

    private function createRequestWithQueryParams(array $queryParams): JsonApiRequest
    {
        $psrRequest = new ServerRequest();
        $psrRequest = $psrRequest->withQueryParams($queryParams);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }
}
