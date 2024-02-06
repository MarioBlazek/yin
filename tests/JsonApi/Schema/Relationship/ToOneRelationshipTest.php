<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Relationship;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Schema\Link\RelationshipLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;
use WoohooLabs\Yin\Tests\JsonApi\Double\DummyData;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResource;

class ToOneRelationshipTest extends TestCase
{
    #[Test]
    public function transformEmpty(): void
    {
        $transformation = new ResourceTransformation(
            new StubResource(),
            [],
            '',
            new StubJsonApiRequest(),
            '',
            '',
            '',
            new DefaultExceptionFactory(),
        );
        $relationship = $this->createRelationship();

        $relationshipObject = $relationship->transform(
            $transformation,
            new ResourceTransformer(),
            new DummyData(),
            [],
        );

        self::assertSame(
            [],
            $relationshipObject,
        );
    }

    #[Test]
    public function transformNull(): void
    {
        $transformation = new ResourceTransformation(
            new StubResource(),
            [],
            '',
            new StubJsonApiRequest(),
            '',
            '',
            '',
            new DefaultExceptionFactory(),
        );
        $relationship = $this->createRelationship([], null, null, $transformation->resource);

        $relationshipObject = $relationship->transform(
            $transformation,
            new ResourceTransformer(),
            new DummyData(),
            [],
        );

        self::assertSame(
            [
                'data' => null,
            ],
            $relationshipObject,
        );
    }

    #[Test]
    public function transform(): void
    {
        $relationship = $this->createRelationship(
            [],
            null,
            [],
            new StubResource('abc', '1'),
        );

        $relationshipObject = $relationship->transform(
            new ResourceTransformation(
                new StubResource(),
                [],
                '',
                new StubJsonApiRequest(),
                '',
                '',
                '',
                new DefaultExceptionFactory(),
            ),
            new ResourceTransformer(),
            new DummyData(),
            [],
        );

        self::assertSame(
            [
                'data' => [
                    'type' => 'abc',
                    'id' => '1',
                ],
            ],
            $relationshipObject,
        );
    }

    private function createRelationship(
        array $meta = [],
        ?RelationshipLinks $links = null,
        ?array $data = [],
        ?ResourceInterface $resource = null
    ): ToOneRelationship {
        return new ToOneRelationship($meta, $links, $data, $resource);
    }
}
