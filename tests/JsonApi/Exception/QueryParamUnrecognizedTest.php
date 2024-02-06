<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\QueryParamUnrecognized;

class QueryParamUnrecognizedTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('400', $errors[0]->getStatus());
    }

    #[Test]
    public function getQueryParam(): void
    {
        $exception = $this->createException('param');

        $queryParam = $exception->getUnrecognizedQueryParam();

        self::assertSame('param', $queryParam);
    }

    private function createException(string $queryParam): QueryParamUnrecognized
    {
        return new QueryParamUnrecognized($queryParam);
    }
}
