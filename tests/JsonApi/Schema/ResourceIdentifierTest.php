<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdentifierIdInvalid;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdentifierIdMissing;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdentifierTypeInvalid;
use WoohooLabs\Yin\JsonApi\Exception\ResourceIdentifierTypeMissing;
use WoohooLabs\Yin\JsonApi\Schema\ResourceIdentifier;

class ResourceIdentifierTest extends TestCase
{
    #[Test]
    public function fromArrayWithMissingType(): void
    {
        $this->expectException(ResourceIdentifierTypeMissing::class);

        ResourceIdentifier::fromArray(['id' => '1'], new DefaultExceptionFactory());
    }

    #[Test]
    public function fromArrayWithNotStringType(): void
    {
        $this->expectException(ResourceIdentifierTypeInvalid::class);

        ResourceIdentifier::fromArray(['type' => 0, 'id' => 1], new DefaultExceptionFactory());
    }

    #[Test]
    public function fromArrayWithMissingId(): void
    {
        $this->expectException(ResourceIdentifierIdMissing::class);

        ResourceIdentifier::fromArray(['type' => 'user'], new DefaultExceptionFactory());
    }

    #[Test]
    public function fromArrayWithNotStringId(): void
    {
        $this->expectException(ResourceIdentifierIdInvalid::class);

        ResourceIdentifier::fromArray(['type' => 'abc', 'id' => 1], new DefaultExceptionFactory());
    }

    #[Test]
    public function fromArrayWithZeroTypeAndId(): void
    {
        $resourceIdentifier = $this->createResourceIdentifier()
            ->setType('0')
            ->setId('0');

        $resourceIdentifierFromArray = ResourceIdentifier::fromArray(
            [
                'type' => '0',
                'id' => '0',
            ],
            new DefaultExceptionFactory(),
        );

        self::assertSame($resourceIdentifier, $resourceIdentifierFromArray);
    }

    #[Test]
    public function fromArray(): void
    {
        $resourceIdentifier = $this->createResourceIdentifier()
            ->setType('user')
            ->setId('1');

        $resourceIdentifierFromArray = ResourceIdentifier::fromArray(
            [
                'type' => 'user',
                'id' => '1',
            ],
            new DefaultExceptionFactory(),
        );

        self::assertSame($resourceIdentifier, $resourceIdentifierFromArray);
    }

    #[Test]
    public function fromArrayWithMeta(): void
    {
        $resourceIdentifier = $this->createResourceIdentifier()
            ->setType('user')
            ->setId('1')
            ->setMeta(['abc' => 'def']);

        $resourceIdentifierFromArray = ResourceIdentifier::fromArray(
            [
                'type' => 'user',
                'id' => '1',
                'meta' => ['abc' => 'def'],
            ],
            new DefaultExceptionFactory(),
        );

        self::assertSame($resourceIdentifier, $resourceIdentifierFromArray);
    }

    #[Test]
    public function getType(): void
    {
        $link = $this->createResourceIdentifier()
            ->setType('abc');

        $id = $link->getType();

        self::assertSame('abc', $id);
    }

    #[Test]
    public function getId(): void
    {
        $link = $this->createResourceIdentifier()
            ->setId('123');

        $id = $link->getId();

        self::assertSame('123', $id);
    }

    private function createResourceIdentifier(): ResourceIdentifier
    {
        return new ResourceIdentifier();
    }
}
