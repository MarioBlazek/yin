<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\QueryParamMalformed;

class QueryParamMalformedTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('', '');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('400', $errors[0]->getStatus());
    }

    #[Test]
    public function getQueryParam(): void
    {
        $exception = $this->createException('sort', ['field' => 'value']);

        self::assertSame('sort', $exception->getMalformedQueryParam());
        self::assertSame(['field' => 'value'], $exception->getMalformedQueryParamValue());
    }

    /**
     * @param mixed $queryParamValue
     */
    private function createException(string $queryParam, $queryParamValue): QueryParamMalformed
    {
        return new QueryParamMalformed($queryParam, $queryParamValue);
    }
}
