<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\SortingUnsupported;

class SortingUnsupportedTest extends TestCase
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
    public function getMessage(): void
    {
        $exception = $this->createException();

        $message = $exception->getMessage();

        self::assertSame('Sorting is not supported!', $message);
    }

    private function createException(): SortingUnsupported
    {
        return new SortingUnsupported();
    }
}
