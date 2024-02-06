<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdentifierTypeMissing;

class ResourceIdentifierTypeMissingTest extends TestCase
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
    public function getResourceIdentifier(): void
    {
        $exception = $this->createException(['id' => '1']);

        $resourceIdentifier = $exception->getResourceIdentifier();

        self::assertSame(['id' => '1'], $resourceIdentifier);
    }

    private function createException(array $resourceIdentifier = []): ResourceIdentifierTypeMissing
    {
        return new ResourceIdentifierTypeMissing($resourceIdentifier);
    }
}
