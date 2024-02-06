<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Link\LinkObject;

class LinkObjectTest extends TestCase
{
    #[Test]
    public function getHref(): void
    {
        $href = 'https://example.com/api/users';

        $link = $this->createLinkObject($href);
        self::assertSame($href, $link->getHref());
    }

    #[Test]
    public function getEmptyMeta(): void
    {
        $href = 'https://example.com/api/users';

        $link = $this->createLinkObject($href);
        self::assertSame([], $link->getMeta());
    }

    #[Test]
    public function getMeta(): void
    {
        $meta = ['abc' => 'def'];

        $link = $this->createLinkWithMeta('', $meta);
        self::assertSame($meta, $link->getMeta());
    }

    #[Test]
    public function transformAbsoluteLinkWithMeta(): void
    {
        $href = 'https://example.com/api/users';
        $meta = ['abc' => 'def'];

        $link = $this->createLinkWithMeta($href, $meta);

        $transformedLink = [
            'href' => $href,
            'meta' => $meta,
        ];
        self::assertSame($transformedLink, $link->transform(''));
    }

    #[Test]
    public function transformRelativeLinkWithoutMeta(): void
    {
        $baseUri = 'https://example.com/api';
        $href = '/users';

        $link = $this->createLinkObject($href);

        $transformedLink = [
            'href' => $baseUri . $href,
        ];
        self::assertSame($transformedLink, $link->transform($baseUri));
    }

    private function createLinkObject(string $href): LinkObject
    {
        return new LinkObject($href);
    }

    private function createLinkWithMeta(string $href, array $meta): LinkObject
    {
        return new LinkObject($href, $meta);
    }
}
