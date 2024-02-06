<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubErrorDocument;

class AbstractErrorDocumentTest extends TestCase
{
    #[Test]
    public function getErrorsWhenEmpty(): void
    {
        $errorDocument = $this->createErrorDocument();

        $errors = $errorDocument->getErrors();

        self::assertSame([], $errors);
    }

    #[Test]
    public function getErrors(): void
    {
        $errorDocument = $this->createErrorDocument()
            ->addError(new Error())
            ->addError(new Error());

        $errors = $errorDocument->getErrors();

        self::assertSame([new Error(), new Error()], $errors);
    }

    #[Test]
    public function getStatusCodeWithOneErrorInDocument(): void
    {
        $errorDocument = $this->createErrorDocument()
            ->addError(Error::create()->setStatus('404'));

        $statusCode = $errorDocument->getStatusCode();

        self::assertSame(404, $statusCode);
    }

    #[Test]
    public function getStatusCodeWithErrorInParameter(): void
    {
        $errorDocument = $this->createErrorDocument()
            ->addError(Error::create());

        $statusCode = $errorDocument->getStatusCode(404);

        self::assertSame(404, $statusCode);
    }

    #[Test]
    public function getStatusCodeWithMultipleErrorsInDocument(): void
    {
        $errorDocument = $this->createErrorDocument()
            ->addError(Error::create()->setStatus('418'))
            ->addError(Error::create()->setStatus('404'));

        $statusCode = $errorDocument->getStatusCode();

        self::assertSame(400, $statusCode);
    }

    private function createErrorDocument(): StubErrorDocument
    {
        return new StubErrorDocument();
    }
}
