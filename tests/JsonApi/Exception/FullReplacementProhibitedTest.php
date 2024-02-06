<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\FullReplacementProhibited;

class FullReplacementProhibitedTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('authors');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('403', $errors[0]->getStatus());
    }

    #[Test]
    public function getRelationshipName(): void
    {
        $exception = $this->createException('authors');

        $relationshipName = $exception->getRelationshipName();

        self::assertSame('authors', $relationshipName);
    }

    private function createException(string $relationshipName): FullReplacementProhibited
    {
        return new FullReplacementProhibited($relationshipName);
    }
}
