<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Pagination;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubPageBasedPaginationProvider;

class PageBasedPaginationProviderTraitTest extends TestCase
{
    #[Test]
    public function getSelfLinkWhenPageIsNegative(): void
    {
        $provider = $this->createProvider(10, -6, 10);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenPageIsZero(): void
    {
        $provider = $this->createProvider(10, 0, 10);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenSizeIsNegative(): void
    {
        $provider = $this->createProvider(10, 1, -1);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenSizeIsZero(): void
    {
        $provider = $this->createProvider(10, 1, 0);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenTotalItemsIsNegative(): void
    {
        $provider = $this->createProvider(-30, 1, 0);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenTotalItemsIsZero(): void
    {
        $provider = $this->createProvider(0, 1, 10);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenPageIsMoreThanLastPage(): void
    {
        $provider = $this->createProvider(30, 31, 10);

        $link = $provider->getSelfLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getSelfLinkWhenPathIsProvided(): void
    {
        $provider = $this->createProvider(10, 1, 10);

        $link = $provider->getSelfLink('https://example.com/api/users', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getSelfLinkWhenPathWithQueryStringSeparatorIsProvided(): void
    {
        $provider = $this->createProvider(10, 1, 10);

        $link = $provider->getSelfLink('https://example.com/api/users?', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getSelfLinkWhenPathAndAdditionalQueryStringIsProvided(): void
    {
        $provider = $this->createProvider(10, 1, 10);

        $link = $provider->getSelfLink('https://example.com/api/users?a=b', 'a=c&b=d');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?a=c&b=d&page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getSelfLinkWhenPathAndAdditionalPaginationQueryStringIsProvided(): void
    {
        $provider = $this->createProvider(10, 1, 10);

        $link = $provider->getSelfLink('https://example.com/api/users', 'page[number]=0&page[size]=0');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getSelfLinkWhenPathWithQueryStringIsProvided(): void
    {
        $provider = $this->createProvider(10, 1, 10);

        $link = $provider->getSelfLink('https://example.com/api/users?a=b', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?a=b&page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getFirstLinkWhenTotalItemsIsZero(): void
    {
        $provider = $this->createProvider(0, 2, 10);

        $link = $provider->getFirstLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getFirstLinkWhenSizeIsZero(): void
    {
        $provider = $this->createProvider(10, 2, 0);

        $link = $provider->getFirstLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getFirstLink(): void
    {
        $provider = $this->createProvider(10, 2, 10);

        $link = $provider->getFirstLink('https://example.com/api/users', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getLastLinkWhenTotalItemsIsZero(): void
    {
        $provider = $this->createProvider(0, 2, 10);

        $link = $provider->getLastLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getLastLinkWhenSizeIsZero(): void
    {
        $provider = $this->createProvider(50, 2, 0);

        $link = $provider->getLastLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getLastLink(): void
    {
        $provider = $this->createProvider(50, 2, 10);

        $link = $provider->getLastLink('https://example.com/api/users', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=5&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getPrevLinkWhenPageIsFirst(): void
    {
        $provider = $this->createProvider(50, 1, 10);

        $link = $provider->getPrevLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getPrevLinkWhenPageIsLast(): void
    {
        $provider = $this->createProvider(50, 5, 10);

        $link = $provider->getPrevLink('https://example.com/api/users', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=4&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getPrevLink(): void
    {
        $provider = $this->createProvider(50, 2, 10);

        $link = $provider->getPrevLink('https://example.com/api/users', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=1&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getNextLinkWhenPageIsLast(): void
    {
        $provider = $this->createProvider(50, 5, 10);

        $link = $provider->getNextLink('https://example.com/api/users', '');

        self::assertNull($link);
    }

    #[Test]
    public function getNextLinkWhenPageIsBeforeLast(): void
    {
        $provider = $this->createProvider(50, 4, 10);

        $link = $provider->getNextLink('https://example.com/api/users', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=5&page%5Bsize%5D=10', $href);
    }

    #[Test]
    public function getNextLink(): void
    {
        $provider = $this->createProvider(50, 2, 10);

        $link = $provider->getNextLink('https://example.com/api/users?', '');
        $href = $link !== null ? $link->getHref() : '';

        self::assertSame('https://example.com/api/users?page%5Bnumber%5D=3&page%5Bsize%5D=10', $href);
    }

    private function createProvider(int $totalItems, int $page, int $size): StubPageBasedPaginationProvider
    {
        return new StubPageBasedPaginationProvider($totalItems, $page, $size);
    }
}
