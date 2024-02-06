<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PageBasedPagination;

class PageBasedPaginationTest extends TestCase
{
    #[Test]
    public function fromPaginationQueryParams(): void
    {
        $pagination = PageBasedPagination::fromPaginationQueryParams(['number' => 1, 'size' => '10']);

        self::assertSame(1, $pagination->getPage());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenMissing(): void
    {
        $pagination = PageBasedPagination::fromPaginationQueryParams([], 1, 10);

        self::assertSame(1, $pagination->getPage());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenEmpty(): void
    {
        $pagination = PageBasedPagination::fromPaginationQueryParams(['number' => '', 'size' => ''], 1, 10);

        self::assertSame(1, $pagination->getPage());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenZero(): void
    {
        $pagination = PageBasedPagination::fromPaginationQueryParams(['number' => '0', 'size' => '0'], 1, 10);

        self::assertSame(0, $pagination->getPage());
        self::assertSame(0, $pagination->getSize());
    }

    #[Test]
    public function fromPaginationQueryParamsWhenNonNumeric(): void
    {
        $pagination = PageBasedPagination::fromPaginationQueryParams(['number' => 'abc', 'size' => 'abc'], 1, 10);

        self::assertSame(1, $pagination->getPage());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function getPage(): void
    {
        $pagination = $this->createPagination(1, 10);

        $page = $pagination->getPage();

        self::assertSame(1, $page);
    }

    #[Test]
    public function getSizeTest(): void
    {
        $pagination = $this->createPagination(1, 10);

        $size = $pagination->getSize();

        self::assertSame(10, $size);
    }

    #[Test]
    public function getPaginationQueryString(): void
    {
        $queryString = PageBasedPagination::getPaginationQueryString(1, 10);

        self::assertSame('page%5Bnumber%5D=1&page%5Bsize%5D=10', $queryString);
    }

    private function createPagination(int $page, int $size): PageBasedPagination
    {
        return new PageBasedPagination($page, $size);
    }
}
