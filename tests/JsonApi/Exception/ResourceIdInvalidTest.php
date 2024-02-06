<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdInvalid;

class ResourceIdInvalidTest extends TestCase
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
    public function getId(): void
    {
        $exception = $this->createException('integer');

        $type = $exception->getType();

        self::assertSame('integer', $type);
    }

    private function createException(string $type): ResourceIdInvalid
    {
        return new ResourceIdInvalid($type);
    }
}
