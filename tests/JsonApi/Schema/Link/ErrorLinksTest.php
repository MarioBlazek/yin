<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Link\ErrorLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;

class ErrorLinksTest extends TestCase
{
    #[Test]
    public function createWithoutBaseUri(): void
    {
        $links = ErrorLinks::createWithoutBaseUri();

        self::assertSame('', $links->getBaseUri());
    }

    #[Test]
    public function createWithBaseUri(): void
    {
        $links = ErrorLinks::createWithBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function setBaseUri(): void
    {
        $links = $this->createErrorLinks();

        $links->setBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function transform(): void
    {
        $linksObject = $this->createErrorLinks(
            '',
            new Link('https://example.com/api/errors/1'),
            [
                new Link('https://example.com/api/errors/type/1'),
                new Link('https://example.com/api/errors/type/2'),
            ],
        );

        $transformedLinks = $linksObject->transform();

        self::assertArrayHasKey('about', $transformedLinks);
        self::assertArrayHasKey('type', $transformedLinks);
        self::assertCount(2, $transformedLinks['type']);
    }

    #[Test]
    public function getAboutWhenEmpty(): void
    {
        $linksObject = $this->createErrorLinks();

        self::assertNull($linksObject->getAbout());
    }

    #[Test]
    public function getAboutWhenNotEmpty(): void
    {
        $about = new Link('https://example.com/about');

        $linksObject = $this->createErrorLinks()->setAbout($about);
        self::assertSame($about, $linksObject->getAbout());
    }

    #[Test]
    public function getTypeWhenEmpty(): void
    {
        $linksObject = $this->createErrorLinks();
        self::assertSame([], $linksObject->getTypes());
    }

    #[Test]
    public function getTypeWhenNotEmpty(): void
    {
        $typeLink = new Link('https://example.com/errors/404');

        $linksObject = $this->createErrorLinks()->addType($typeLink);

        self::assertContains($typeLink, $linksObject->getTypes());
    }

    #[Test]
    public function setTypes(): void
    {
        $typeLink1 = new Link('https://example.com/errors/404');
        $typeLink2 = new Link('https://example.com/errors/405');

        $linksObject = $this->createErrorLinks()->setTypes([$typeLink1, $typeLink2]);

        self::assertCount(2, $linksObject->getTypes());
        self::assertSame($typeLink1, $linksObject->getTypes()[0]);
        self::assertSame($typeLink2, $linksObject->getTypes()[1]);
    }

    #[Test]
    public function setTypesWithSameHref(): void
    {
        $typeLink = new Link('https://example.com/errors/404');

        $linksObject = $this->createErrorLinks()->setTypes([$typeLink, $typeLink]);

        self::assertCount(1, $linksObject->getTypes());
        self::assertSame($typeLink, $linksObject->getTypes()[0]);
    }

    private function createErrorLinks(string $baseUri = '', ?Link $about = null, array $types = []): ErrorLinks
    {
        return new ErrorLinks($baseUri, $about, $types);
    }
}
