<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\RequestBodyInvalidJson;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;

class RequestBodyInvalidJsonTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException();

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('400', $errors[0]->getStatus());
    }

    #[Test]
    public function getErrorDocumentWhenNotIncludeOriginal(): void
    {
        $exception = $this->createException('abc', '', false);

        $meta = $exception->getErrorDocument()->getMeta();

        self::assertEmpty($meta);
    }

    #[Test]
    public function getErrorDocumentWhenIncludeOriginal(): void
    {
        $exception = $this->createException('abc', '', true);

        $meta = $exception->getErrorDocument()->getMeta();

        self::assertSame(['original' => 'abc'], $meta);
    }

    #[Test]
    public function getLintMessage(): void
    {
        $exception = $this->createException('', 'abc');

        $lintMessage = $exception->getLintMessage();

        self::assertSame('abc', $lintMessage);
    }

    private function createException(string $body = '', string $lintMessage = '', bool $includeOriginal = false): RequestBodyInvalidJson
    {
        $request = StubJsonApiRequest::create();
        $request->getBody()->write($body);

        return new RequestBodyInvalidJson($request, $lintMessage, $includeOriginal);
    }
}
