<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdentifierIdMissing;

class ResourceIdentifierIdMissingTest extends TestCase
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
        $exception = $this->createException(['type' => 'abc']);

        $resourceIdentifier = $exception->getResourceIdentifier();

        self::assertSame(['type' => 'abc'], $resourceIdentifier);
    }

    private function createException(array $resourceIdentifier = []): ResourceIdentifierIdMissing
    {
        return new ResourceIdentifierIdMissing($resourceIdentifier);
    }
}
