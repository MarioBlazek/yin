<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Request\Pagination\OffsetBasedPagination;

class OffsetBasedPaginationTest extends TestCase
{
    #[Test]
    public function fromPaginationQueryParams(): void
    {
        $pagination = OffsetBasedPagination::fromPaginationQueryParams(['offset' => 1, 'limit' => 10]);

        self::assertSame(1, $pagination->getOffset());
        self::assertSame(10, $pagination->getLimit());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenMissing(): void
    {
        $pagination = OffsetBasedPagination::fromPaginationQueryParams([], 1, 10);

        self::assertSame(1, $pagination->getOffset());
        self::assertSame(10, $pagination->getLimit());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenEmpty(): void
    {
        $pagination = OffsetBasedPagination::fromPaginationQueryParams(['offset' => '', 'limit' => ''], 1, 10);

        self::assertSame(1, $pagination->getOffset());
        self::assertSame(10, $pagination->getLimit());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenZero(): void
    {
        $pagination = OffsetBasedPagination::fromPaginationQueryParams(['offset' => '0', 'limit' => '0'], 1, 10);

        self::assertSame(0, $pagination->getOffset());
        self::assertSame(0, $pagination->getLimit());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenNonNumeric(): void
    {
        $pagination = OffsetBasedPagination::fromPaginationQueryParams(['offset' => 'abc', 'limit' => 'abc'], 1, 10);

        self::assertSame(1, $pagination->getOffset());
        self::assertSame(10, $pagination->getLimit());
    }

    #[Test]
    public function getOffset(): void
    {
        $pagination = $this->createPagination(1, 10);

        $offset = $pagination->getOffset();

        self::assertSame(1, $offset);
    }

    #[Test]
    public function getLimit(): void
    {
        $pagination = $this->createPagination(1, 10);

        $limit = $pagination->getLimit();

        self::assertSame(10, $limit);
    }

    #[Test]
    public function getPaginationQueryString(): void
    {
        $queryString = OffsetBasedPagination::getPaginationQueryString(1, 10);

        self::assertSame('page%5Boffset%5D=1&page%5Blimit%5D=10', $queryString);
    }

    private function createPagination(int $offset, int $limit): OffsetBasedPagination
    {
        return new OffsetBasedPagination($offset, $limit);
    }
}
