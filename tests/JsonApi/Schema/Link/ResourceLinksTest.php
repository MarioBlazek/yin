<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;

class ResourceLinksTest extends TestCase
{
    #[Test]
    public function createWithoutBaseUri(): void
    {
        $links = ResourceLinks::createWithoutBaseUri();

        self::assertSame('', $links->getBaseUri());
    }

    #[Test]
    public function createWithBaseUri(): void
    {
        $links = ResourceLinks::createWithBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function setBaseUri(): void
    {
        $links = $this->createResourceLinks();

        $links->setBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function transform(): void
    {
        $links = $this->createResourceLinks('', new Link('https://example.com/articles/1'));

        self::assertArrayHasKey('self', $links->transform());
    }

    #[Test]
    public function getSelfWhenEmpty(): void
    {
        $links = $this->createResourceLinks();

        self::assertNull($links->getSelf());
    }

    #[Test]
    public function getSelfWhenNotEmpty(): void
    {
        $self = new Link('https://example.com/api/users');

        $links = $this->createResourceLinks()->setSelf($self);

        self::assertSame($self, $links->getSelf());
    }

    #[Test]
    public function getLinkWhenEmpty(): void
    {
        $links = $this->createResourceLinks();

        self::assertNull($links->getLink('self'));
    }

    #[Test]
    public function getLinkWhenNotEmpty(): void
    {
        $self = new Link('https://example.com/api/users');

        $links = $this->createResourceLinks()->setSelf($self);

        self::assertSame($self, $links->getLink('self'));
    }

    private function createResourceLinks(string $baseUri = '', ?Link $self = null): ResourceLinks
    {
        return new ResourceLinks($baseUri, $self);
    }
}
