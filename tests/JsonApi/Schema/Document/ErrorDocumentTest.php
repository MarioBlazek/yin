<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

class ErrorDocumentTest extends TestCase
{
    #[Test]
    public function getJsonApi(): void
    {
        $errorDocument = $this->createErrorDocument();

        $errorDocument->setJsonApi(new JsonApiObject('1.0'));

        self::assertEquals(new JsonApiObject('1.0'), $errorDocument->getJsonApi());
    }

    #[Test]
    public function getMeta(): void
    {
        $errorDocument = $this->createErrorDocument();

        $errorDocument->setMeta(['abc' => 'def']);

        self::assertEquals(['abc' => 'def'], $errorDocument->getMeta());
    }

    #[Test]
    public function getLinks(): void
    {
        $errorDocument = $this->createErrorDocument();

        $errorDocument->setLinks(new DocumentLinks('https://example.com'));

        self::assertEquals(new DocumentLinks('https://example.com'), $errorDocument->getLinks());
    }

    private function createErrorDocument(): ErrorDocument
    {
        return new ErrorDocument();
    }
}
