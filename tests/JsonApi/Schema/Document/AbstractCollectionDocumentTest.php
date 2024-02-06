<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubCollectionDocument;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResource;

class AbstractCollectionDocumentTest extends TestCase
{
    #[Test]
    public function getResource(): void
    {
        $resource = new StubResource();

        $collectionDocument = $this->createCollectionDocument($resource);

        self::assertSame($resource, $collectionDocument->getResource());
    }

    #[Test]
    public function hasItemsTrue(): void
    {
        $resource = new StubResource();

        $collectionDocument = $this->createCollectionDocument($resource, [[], []]);

        self::assertTrue($collectionDocument->getHasItems());
    }

    #[Test]
    public function hasItemsFalse(): void
    {
        $resource = new StubResource();

        $collectionDocument = $this->createCollectionDocument($resource, []);

        self::assertFalse($collectionDocument->getHasItems());
    }

    #[Test]
    public function getItemsFalse(): void
    {
        $resource = new StubResource();

        $collectionDocument = $this->createCollectionDocument($resource, []);

        self::assertFalse($collectionDocument->getHasItems());
    }

    private function createCollectionDocument(?ResourceInterface $resource = null, iterable $object = []): StubCollectionDocument
    {
        return new StubCollectionDocument(null, [], null, $resource, $object);
    }
}
