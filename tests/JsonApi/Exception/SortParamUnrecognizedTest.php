<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\SortParamUnrecognized;

class SortParamUnrecognizedTest extends TestCase
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
    public function getSortParam(): void
    {
        $exception = $this->createException('param');

        $sortParam = $exception->getSortParam();

        self::assertSame('param', $sortParam);
    }

    private function createException(string $sortParam): SortParamUnrecognized
    {
        return new SortParamUnrecognized($sortParam);
    }
}
