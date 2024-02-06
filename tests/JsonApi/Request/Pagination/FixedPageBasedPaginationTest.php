<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedPageBasedPagination;

class FixedPageBasedPaginationTest extends TestCase
{
    #[Test]
    public function fromPaginationQueryParams(): void
    {
        $pagination = FixedPageBasedPagination::fromPaginationQueryParams(['number' => 1]);

        self::assertSame(1, $pagination->getPage());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenMissing(): void
    {
        $pagination = FixedPageBasedPagination::fromPaginationQueryParams([], 1);

        self::assertSame(1, $pagination->getPage());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenEmpty(): void
    {
        $pagination = FixedPageBasedPagination::fromPaginationQueryParams(['number' => ''], 1);

        self::assertSame(1, $pagination->getPage());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenZero(): void
    {
        $pagination = FixedPageBasedPagination::fromPaginationQueryParams(['number' => '0'], 1);

        self::assertSame(0, $pagination->getPage());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenNonNumeric(): void
    {
        $pagination = FixedPageBasedPagination::fromPaginationQueryParams(['number' => 'abc'], 1);

        self::assertSame(1, $pagination->getPage());
    }

    #[Test]
    public function getPage(): void
    {
        $pagination = $this->createPagination(1);

        $page = $pagination->getPage();

        self::assertSame(1, $page);
    }

    #[Test]
    public function getPaginationQueryString(): void
    {
        $queryString = FixedPageBasedPagination::getPaginationQueryString(1);

        self::assertSame('page%5Bnumber%5D=1', $queryString);
    }

    private function createPagination(int $page): FixedPageBasedPagination
    {
        return new FixedPageBasedPagination($page);
    }
}
