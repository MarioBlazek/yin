<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Relationship;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Schema\Link\RelationshipLinks;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;
use WoohooLabs\Yin\Tests\JsonApi\Double\DummyData;
use WoohooLabs\Yin\Tests\JsonApi\Double\FakeRelationship;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResource;

class AbstractRelationshipTest extends TestCase
{
    #[Test]
    public function createWithData(): void
    {
        $relationship = FakeRelationship::createWithData([], new StubResource());

        $data = $relationship->getRelationshipData();

        self::assertSame([], $data);
    }

    #[Test]
    public function createWithLinks(): void
    {
        $relationship = FakeRelationship::createWithLinks(new RelationshipLinks());

        $links = $relationship->getLinks();

        self::assertNotNull($links);
    }

    #[Test]
    public function createWithMeta(): void
    {
        $relationship = FakeRelationship::createWithMeta(['abc' => 'def']);

        $meta = $relationship->getMeta();

        self::assertSame(['abc' => 'def'], $meta);
    }

    #[Test]
    public function setLinks(): void
    {
        $relationship = FakeRelationship::create();

        $relationship->setLinks(new RelationshipLinks());

        self::assertNotNull($relationship->getLinks());
    }

    //    #[\PHPUnit\Framework\Attributes\Test]
    //    public function setData(int|string $dataName, array $data): void
    //    {
    //        $relationship = $this->createRelationship();
    //
    //        $relationship->setData(["id" => 1], new StubResource());
    //
    //        $this->assertEquals(["id" => 1], $relationship->getRelationshipData());
    //    }

    #[Test]
    public function setDataAsCallable(): void
    {
        $relationship = $this->createRelationship();

        $relationship->setDataAsCallable(
            static fn (): array => ['id' => 1],
            new StubResource(),
        );
        $data = $relationship->getRelationshipData();

        self::assertSame(
            ['id' => 1],
            $data,
        );
    }

    #[Test]
    public function dataNotOmittedByDefault(): void
    {
        $relationship = $this->createRelationship();

        $isDataOmittedWhenNotIncluded = $relationship->isOmitDataWhenNotIncluded();

        self::assertFalse($isDataOmittedWhenNotIncluded);
    }

    #[Test]
    public function omitDataWhenNotIncluded(): void
    {
        $relationship = $this->createRelationship();

        $relationship->omitDataWhenNotIncluded();

        self::assertTrue($relationship->isOmitDataWhenNotIncluded());
    }

    #[Test]
    public function transformWithMeta(): void
    {
        $relationship = $this->createRelationship()
            ->setMeta(['abc' => 'def']);

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
                'meta' => [
                    'abc' => 'def',
                ],
                'data' => [],
            ],
            $relationshipObject,
        );
    }

    #[Test]
    public function transformWithLinks(): void
    {
        $relationship = $this->createRelationship()
            ->setLinks(new RelationshipLinks());

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
                'links' => [],
                'data' => [],
            ],
            $relationshipObject,
        );
    }

    #[Test]
    public function transformWhenNotIncludedField(): void
    {
        $relationship = $this->createRelationship();

        $relationshipObject = $relationship->transform(
            new ResourceTransformation(
                new StubResource('user1'),
                [],
                'user1',
                new StubJsonApiRequest(['fields' => ['user1' => '']]),
                '',
                'rel',
                'rel',
                new DefaultExceptionFactory(),
            ),
            new ResourceTransformer(),
            new DummyData(),
            [],
        );

        self::assertNull($relationshipObject);
    }

    #[Test]
    public function transformWithEmptyData(): void
    {
        $relationship = $this->createRelationship();

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
                'data' => [],
            ],
            $relationshipObject,
        );
    }

    #[Test]
    public function transformWithEmptyOmittedData(): void
    {
        $relationship = $this->createRelationship()
            ->omitDataWhenNotIncluded();

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
            [],
            $relationshipObject,
        );
    }

    #[Test]
    public function transformWithEmptyOmittedDataWhenRelationship(): void
    {
        $relationship = $this->createRelationship()
            ->omitDataWhenNotIncluded();

        $relationshipObject = $relationship->transform(
            new ResourceTransformation(
                new StubResource(),
                [],
                '',
                new StubJsonApiRequest(),
                '',
                'dummy',
                'dummy',
                new DefaultExceptionFactory(),
            ),
            new ResourceTransformer(),
            new DummyData(),
            [],
        );

        self::assertSame(
            [
                'data' => [],
            ],
            $relationshipObject,
        );
    }

    private function createRelationship(): FakeRelationship
    {
        return new FakeRelationship();
    }
}
