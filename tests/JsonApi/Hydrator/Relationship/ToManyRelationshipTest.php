<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Hydrator\Relationship;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\ResourceIdentifier;

class ToManyRelationshipTest extends TestCase
{
    #[Test]
    public function getResourceIdentifiers(): void
    {
        $resourceIdentifier1 = (new ResourceIdentifier())->setType('user')->setId('1');
        $resourceIdentifier2 = (new ResourceIdentifier())->setType('user')->setId('2');

        $relationship = $this->createRelationship()
            ->addResourceIdentifier($resourceIdentifier1)
            ->addResourceIdentifier($resourceIdentifier2);
        self::assertSame([$resourceIdentifier1, $resourceIdentifier2], $relationship->getResourceIdentifiers());
    }

    #[Test]
    public function getResourceIdentifierTypes(): void
    {
        $type = 'user';
        $resourceIdentifier1 = (new ResourceIdentifier())->setType($type)->setId('1');
        $resourceIdentifier2 = (new ResourceIdentifier())->setType($type)->setId('2');

        $relationship = $this->createRelationship()
            ->addResourceIdentifier($resourceIdentifier1)
            ->addResourceIdentifier($resourceIdentifier2);
        self::assertSame([$type, $type], $relationship->getResourceIdentifierTypes());
    }

    #[Test]
    public function getResourceIdentifierIds(): void
    {
        $id1 = '1';
        $resourceIdentifier1 = (new ResourceIdentifier())->setType('user')->setId($id1);
        $id2 = '2';
        $resourceIdentifier2 = (new ResourceIdentifier())->setType('user')->setId($id2);

        $relationship = $this->createRelationship()
            ->addResourceIdentifier($resourceIdentifier1)
            ->addResourceIdentifier($resourceIdentifier2);
        self::assertSame([$id1, $id2], $relationship->getResourceIdentifierIds());
    }

    #[Test]
    public function isEmptyIsFalse(): void
    {
        $relationship = $this->createRelationship()
            ->addResourceIdentifier(new ResourceIdentifier());

        self::assertFalse($relationship->isEmpty());
    }

    #[Test]
    public function isEmptyIsTrue(): void
    {
        $relationship = $this->createRelationship();

        self::assertTrue($relationship->isEmpty());
    }

    private function createRelationship(): ToManyRelationship
    {
        return new ToManyRelationship();
    }
}
