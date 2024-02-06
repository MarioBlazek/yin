<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipNotExists;

class RelationshipNotExistsTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException();

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('404', $errors[0]->getStatus());
    }

    #[Test]
    public function getRelationship(): void
    {
        $exception = $this->createException('rel');

        $relationship = $exception->getRelationship();

        self::assertSame('rel', $relationship);
    }

    private function createException(string $relationship = ''): RelationshipNotExists
    {
        return new RelationshipNotExists($relationship);
    }
}
