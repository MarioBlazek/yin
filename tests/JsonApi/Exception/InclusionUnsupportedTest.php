<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\InclusionUnsupported;

class InclusionUnsupportedTest extends TestCase
{
    #[Test]
    public function getMessage(): void
    {
        $exception = $this->createException();
        self::assertSame('Inclusion is not supported!', $exception->getMessage());
    }

    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException();

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('400', $errors[0]->getStatus());
    }

    private function createException(): InclusionUnsupported
    {
        return new InclusionUnsupported();
    }
}
