<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\RelationshipLinks;

class RelationshipLinksTest extends TestCase
{
    #[Test]
    public function createWithoutBaseUri(): void
    {
        $links = RelationshipLinks::createWithoutBaseUri();

        self::assertSame('', $links->getBaseUri());
    }

    #[Test]
    public function createWithBaseUri(): void
    {
        $links = RelationshipLinks::createWithBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function setBaseUri(): void
    {
        $links = $this->createRelationshipLinks();

        $links->setBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function transform(): void
    {
        $links = $this->createRelationshipLinks(
            '',
            new Link('https://example.com/articles/1/relationships/author'),
            new Link('https://example.com/articles/1/author'),
        );

        $transformedLinks = $links->transform();

        self::assertArrayHasKey('self', $transformedLinks);
        self::assertArrayHasKey('related', $transformedLinks);
    }

    #[Test]
    public function getSelfWhenEmpty(): void
    {
        $links = $this->createRelationshipLinks();

        self::assertNull($links->getSelf());
    }

    #[Test]
    public function getSelfWhenNotEmpty(): void
    {
        $self = new Link('https://example.com/api/users');

        $links = $this->createRelationshipLinks()->setSelf($self);

        self::assertSame($self, $links->getSelf());
    }

    #[Test]
    public function getRelatedWhenEmpty(): void
    {
        $links = $this->createRelationshipLinks();

        self::assertNull($links->getRelated());
    }

    #[Test]
    public function getRelatedWhenNotEmpty(): void
    {
        $related = new Link('https://example.com/articles/1/author');

        $links = $this->createRelationshipLinks()->setRelated($related);

        self::assertSame($related, $links->getRelated());
    }

    #[Test]
    public function getLinkWhenEmpty(): void
    {
        $links = $this->createRelationshipLinks();

        self::assertNull($links->getLink('self'));
    }

    #[Test]
    public function getLinkWhenNotEmpty(): void
    {
        $self = new Link('https://example.com/api/users');

        $links = $this->createRelationshipLinks()->setSelf($self);

        self::assertSame($self, $links->getLink('self'));
    }

    private function createRelationshipLinks(string $baseUri = '', ?Link $self = null, ?Link $related = null): RelationshipLinks
    {
        return new RelationshipLinks($baseUri, $self, $related);
    }
}
