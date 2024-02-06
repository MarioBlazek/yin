<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ResponseBodyInvalidJsonApi;

class ResponseBodyInvalidJsonApiTest extends TestCase
{
    #[Test]
    public function getErrorsWithTwoErrors(): void
    {
        $exception = $this->createException(
            '',
            [
                [
                    'message' => 'abc',
                    'property' => 'property1',
                ],
                [
                    'message' => 'cde',
                    'property' => '',
                ],
            ],
        );

        $errors = $exception->getErrorDocument()->getErrors();
        $source = $errors[0]->getSource();

        self::assertCount(2, $errors);
        self::assertSame('500', $errors[0]->getStatus());
        self::assertSame('Abc', $errors[0]->getDetail());
        self::assertSame('property1', $source !== null ? $source->getPointer() : null);
        self::assertSame('500', $errors[1]->getStatus());
        self::assertSame('Cde', $errors[1]->getDetail());
        self::assertNull($errors[1]->getSource());
    }

    #[Test]
    public function getErrorDocumentWhenNotIncludeOriginal(): void
    {
        $exception = $this->createException('abc', [], false);

        $meta = $exception->getErrorDocument()->getMeta();

        self::assertEmpty($meta);
    }

    #[Test]
    public function getErrorDocumentWhenIncludeOriginal(): void
    {
        $exception = $this->createException('"abc"', [], true);

        $meta = $exception->getErrorDocument()->getMeta();

        self::assertSame(['original' => 'abc'], $meta);
    }

    #[Test]
    public function getValidationErrors(): void
    {
        $exception = $this->createException('', ['abc', 'def']);

        $validationErrors = $exception->getValidationErrors();

        self::assertSame(['abc', 'def'], $validationErrors);
    }

    private function createException(string $body = '', array $validationErrors = [], bool $includeOriginal = false): ResponseBodyInvalidJsonApi
    {
        $response = new Response();
        $response->getBody()->write($body);

        return new ResponseBodyInvalidJsonApi($response, $validationErrors, $includeOriginal);
    }
}
