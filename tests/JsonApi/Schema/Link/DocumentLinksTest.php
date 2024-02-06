<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ProfileLinkObject;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubPaginationLinkProvider;

class DocumentLinksTest extends TestCase
{
    #[Test]
    public function createWithoutBaseUri(): void
    {
        $links = DocumentLinks::createWithoutBaseUri([]);

        self::assertSame('', $links->getBaseUri());
    }

    #[Test]
    public function createWithBaseUri(): void
    {
        $links = DocumentLinks::createWithBaseUri('https://example.com', []);

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function setBaseUri(): void
    {
        $links = $this->createLinks();

        $links->setBaseUri('https://example.com');

        self::assertSame('https://example.com', $links->getBaseUri());
    }

    #[Test]
    public function transform(): void
    {
        $links = $this->createLinks(
            '',
            [
                'self' => new Link('https://example.com/articles/1/relationships/author'),
                'related' => new Link('https://example.com/articles/1/author'),
            ],
        );

        $transformedLinks = $links->transform();

        self::assertArrayHasKey('self', $transformedLinks);
        self::assertArrayHasKey('related', $transformedLinks);
    }

    #[Test]
    public function getSelfWhenEmpty(): void
    {
        $links = $this->createLinks();

        self::assertNull($links->getSelf());
    }

    #[Test]
    public function getSelfWhenNotEmpty(): void
    {
        $self = new Link('https://example.com/api/users');

        $links = $this->createLinks()->setSelf($self);

        self::assertSame($self, $links->getSelf());
    }

    #[Test]
    public function getRelatedWhenNotEmpty(): void
    {
        $related = new Link('https://example.com/api/users');

        $links = $this->createLinks()->setRelated($related);

        self::assertSame($related, $links->getRelated());
    }

    #[Test]
    public function getFirstWhenEmpty(): void
    {
        $linksObject = $this->createLinks();

        self::assertNull($linksObject->getFirst());
    }

    #[Test]
    public function getFirstWhenNotEmpty(): void
    {
        $first = new Link('https://example.com/api/users?page[number]=1');

        $links = $this->createLinks()->setFirst($first);

        self::assertSame($first, $links->getFirst());
    }

    #[Test]
    public function getLastWhenNotEmpty(): void
    {
        $last = new Link('https://example.com/api/users?page[number]=10');

        $links = $this->createLinks()->setLast($last);

        self::assertSame($last, $links->getLast());
    }

    #[Test]
    public function getPrevWhenNotEmpty(): void
    {
        $prev = new Link('https://example.com/api/users?page[number]=4');

        $links = $this->createLinks()->setPrev($prev);

        self::assertSame($prev, $links->getPrev());
    }

    #[Test]
    public function getNextWhenNotEmpty(): void
    {
        $next = new Link('https://example.com/api/users?page[number]=6');

        $links = $this->createLinks()->setNext($next);

        self::assertSame($next, $links->getNext());
    }

    #[Test]
    public function setPagination(): void
    {
        $pagination = new StubPaginationLinkProvider();

        $links = $this->createLinks()->setPagination('https://example.com/api/users/', $pagination);

        self::assertEquals(new Link('https://example.com/api/users/self'), $links->getSelf());
        self::assertEquals(new Link('https://example.com/api/users/first'), $links->getFirst());
        self::assertEquals(new Link('https://example.com/api/users/last'), $links->getLast());
        self::assertEquals(new Link('https://example.com/api/users/prev'), $links->getPrev());
        self::assertEquals(new Link('https://example.com/api/users/next'), $links->getNext());
    }

    #[Test]
    public function getLink(): void
    {
        $self = new Link('https://example.com/api/users');

        $links = $this->createLinks()->setLink('self', $self);

        self::assertSame($self, $links->getLink('self'));
    }

    #[Test]
    public function getMultipleLinks(): void
    {
        $self = new Link('https://example.com/api/users/1');
        $related = new Link('https://example.com/api/people/1');
        $links = ['self' => $self, 'related' => $related];

        $links = $this->createLinks()->setLinks($links);

        self::assertSame($self, $links->getLink('self'));
        self::assertSame($related, $links->getLink('related'));
    }

    #[Test]
    public function getProfiles(): void
    {
        $profile1 = new ProfileLinkObject('href1');
        $profile2 = new ProfileLinkObject('href2');

        $links = $this->createLinks('', [], [$profile1, $profile2]);

        self::assertCount(2, $links->getProfiles());
        self::assertSame($profile1, $links->getProfiles()[0]);
        self::assertSame($profile2, $links->getProfiles()[1]);
    }

    #[Test]
    public function addProfilesWithSameHref(): void
    {
        $profile = new ProfileLinkObject('');

        $links = $this->createLinks('', [])
            ->addProfile($profile)
            ->addProfile($profile);

        self::assertCount(1, $links->getProfiles());
    }

    /**
     * @param Link[] $links
     * @param ProfileLinkObject[] $profiles
     */
    private function createLinks(string $baseUri = '', array $links = [], array $profiles = []): DocumentLinks
    {
        return new DocumentLinks($baseUri, $links, $profiles);
    }
}
