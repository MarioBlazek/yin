<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Hydrator\Relationship;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\ResourceIdentifier;

class ToOneRelationshipTest extends TestCase
{
    #[Test]
    public function getResourceIdentifierWhenSetInConstructor(): void
    {
        $resourceIdentifier = (new ResourceIdentifier())->setType('user')->setId('1');

        $relationship = $this->createRelationship($resourceIdentifier);
        self::assertSame($resourceIdentifier, $relationship->getResourceIdentifier());
    }

    #[Test]
    public function setResourceIdentifier(): void
    {
        $resourceIdentifier = (new ResourceIdentifier())->setType('user')->setId('1');

        $relationship = $this->createRelationship()->setResourceIdentifier($resourceIdentifier);
        self::assertSame($resourceIdentifier, $relationship->getResourceIdentifier());
    }

    #[Test]
    public function isEmptyIsFalse(): void
    {
        $relationship = $this->createRelationship(new ResourceIdentifier());

        self::assertFalse($relationship->isEmpty());
    }

    #[Test]
    public function isEmptyIsTrue(): void
    {
        $relationship = $this->createRelationship();

        self::assertTrue($relationship->isEmpty());
    }

    private function createRelationship(?ResourceIdentifier $resourceIdentifier = null): ToOneRelationship
    {
        return new ToOneRelationship($resourceIdentifier);
    }
}
