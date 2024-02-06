<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeUnacceptable;

class ResourceTypeUnacceptableTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('', []);

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('409', $errors[0]->getStatus());
    }

    #[Test]
    public function getCurrentType(): void
    {
        $exception = $this->createException('book', []);

        $type = $exception->getCurrentType();

        self::assertSame('book', $type);
    }

    #[Test]
    public function getAcceptedTypes(): void
    {
        $exception = $this->createException('', ['book']);

        $types = $exception->getAcceptedTypes();

        self::assertSame(['book'], $types);
    }

    private function createException(string $type, array $acceptedTypes): ResourceTypeUnacceptable
    {
        return new ResourceTypeUnacceptable($type, $acceptedTypes);
    }
}
