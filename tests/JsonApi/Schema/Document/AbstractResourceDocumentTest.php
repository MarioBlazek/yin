<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ResourceDocumentInterface;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceDocumentTransformation;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResourceDocument;

class AbstractResourceDocumentTest extends TestCase
{
    #[Test]
    public function initializeTransformation(): void
    {
        $document = $this->createDocument();
        $transformation = $this->createTransformation($document);

        $document->initializeTransformation($transformation);

        self::assertSame($transformation->request, $document->getRequest());
        self::assertSame($transformation->object, $document->getObject());
        self::assertSame($transformation->exceptionFactory, $document->getExceptionFactory());
    }

    #[Test]
    public function clearTransformation(): void
    {
        $document = $this->createDocument();
        $transformation = $this->createTransformation($document);

        $document->initializeTransformation($transformation);
        $document->clearTransformation();

        self::assertNotNull($document->getRequest());
        self::assertNotNull($document->getObject());
        self::assertNotNull($document->getExceptionFactory());
    }

    private function createTransformation(ResourceDocumentInterface $document): ResourceDocumentTransformation
    {
        return new ResourceDocumentTransformation(
            $document,
            [],
            new StubJsonApiRequest(),
            '',
            '',
            [],
            new DefaultExceptionFactory(),
        );
    }

    private function createDocument(
        ?JsonApiObject $jsonApi = null,
        array $meta = [],
        ?DocumentLinks $links = null,
        ?DataInterface $data = null,
        array $relationshipResponseContent = []
    ): StubResourceDocument {
        return new StubResourceDocument(
            $jsonApi,
            $meta,
            $links,
            $data,
            $relationshipResponseContent,
        );
    }
}
