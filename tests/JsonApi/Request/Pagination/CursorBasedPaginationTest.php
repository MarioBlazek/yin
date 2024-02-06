<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Request\Pagination\CursorBasedPagination;

class CursorBasedPaginationTest extends TestCase
{
    #[Test]
    public function fromPaginationQueryParams(): void
    {
        $pagination = CursorBasedPagination::fromPaginationQueryParams(['cursor' => 'abc', 'size' => '10']);

        self::assertSame('abc', $pagination->getCursor());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function fromMissingPaginationQueryParams(): void
    {
        $pagination = CursorBasedPagination::fromPaginationQueryParams([], 'abc', 10);

        self::assertSame('abc', $pagination->getCursor());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function fromEmptyPaginationQueryParams(): void
    {
        $pagination = CursorBasedPagination::fromPaginationQueryParams(['cursor' => '', 'size' => ''], 'abc', 10);

        self::assertSame('', $pagination->getCursor());
        self::assertSame(10, $pagination->getSize());
    }

    #[Test]
    public function getCursor(): void
    {
        $pagination = $this->createPagination('abc', 10);

        $cursor = $pagination->getCursor();
        $size = $pagination->getSize();

        self::assertSame('abc', $cursor);
        self::assertSame(10, $size);
    }

    #[Test]
    public function getPaginationQueryString(): void
    {
        $queryString = CursorBasedPagination::getPaginationQueryString('abc', 10);

        self::assertSame('page%5Bcursor%5D=abc&page%5Bsize%5D=10', $queryString);
    }

    private function createPagination(string $cursor, int $page = 0): CursorBasedPagination
    {
        return new CursorBasedPagination($cursor, $page);
    }
}
