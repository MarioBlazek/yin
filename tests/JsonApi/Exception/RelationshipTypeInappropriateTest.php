<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipTypeInappropriate;

class RelationshipTypeInappropriateTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('', '', '');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('400', $errors[0]->getStatus());
    }

    #[Test]
    public function getRelationshipName(): void
    {
        $exception = $this->createException('rel', '', '');

        $relationshipName = $exception->getRelationshipName();

        self::assertSame('rel', $relationshipName);
    }

    #[Test]
    public function getCurrentRelationshipType(): void
    {
        $exception = $this->createException('', 'type', '');

        $relationshipType = $exception->getCurrentRelationshipType();

        self::assertSame('type', $relationshipType);
    }

    #[Test]
    public function getExpectedRelationshipType(): void
    {
        $exception = $this->createException('', '', 'type');

        $relationshipType = $exception->getExpectedRelationshipType();

        self::assertSame('type', $relationshipType);
    }

    private function createException(string $name, string $type, string $expectedType): RelationshipTypeInappropriate
    {
        return new RelationshipTypeInappropriate($name, $type, $expectedType);
    }
}
