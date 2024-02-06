<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedCursorBasedPagination;

class FixedCursorBasedPaginationTest extends TestCase
{
    #[Test]
    public function fromPaginationQueryParams(): void
    {
        $pagination = FixedCursorBasedPagination::fromPaginationQueryParams(['cursor' => 'abc']);

        self::assertSame('abc', $pagination->getCursor());
    }

    #[Test]
    public function fromMissingPaginationQueryParams(): void
    {
        $pagination = FixedCursorBasedPagination::fromPaginationQueryParams([], 'abc');

        self::assertSame('abc', $pagination->getCursor());
    }

    #[Test]
    public function fromEmptyPaginationQueryParams(): void
    {
        $pagination = FixedCursorBasedPagination::fromPaginationQueryParams(['cursor' => ''], 'abc');

        self::assertSame('', $pagination->getCursor());
    }

    #[Test]
    public function getCursor(): void
    {
        $pagination = $this->createPagination('abc');

        $cursor = $pagination->getCursor();

        self::assertSame('abc', $cursor);
    }

    #[Test]
    public function getPaginationQueryString(): void
    {
        $queryString = FixedCursorBasedPagination::getPaginationQueryString('abc');

        self::assertSame('page%5Bcursor%5D=abc', $queryString);
    }

    private function createPagination(string $cursor): FixedCursorBasedPagination
    {
        return new FixedCursorBasedPagination($cursor);
    }
}
