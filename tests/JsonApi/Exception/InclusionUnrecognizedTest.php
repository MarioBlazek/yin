<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\InclusionUnrecognized;

class InclusionUnrecognizedTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException([]);

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('400', $errors[0]->getStatus());
    }

    #[Test]
    public function getIncludes(): void
    {
        $exception = $this->createException(['a', 'b', 'c']);

        $includes = $exception->getUnrecognizedIncludes();

        self::assertSame(['a', 'b', 'c'], $includes);
    }

    private function createException(array $includes): InclusionUnrecognized
    {
        return new InclusionUnrecognized($includes);
    }
}
