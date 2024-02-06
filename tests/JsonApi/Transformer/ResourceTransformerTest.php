<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Transformer;

use Laminas\Diactoros\ServerRequest as DiactorosServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Exception\InclusionUnrecognized;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipNotExists;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;
use WoohooLabs\Yin\Tests\JsonApi\Double\DummyData;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResource;

class ResourceTransformerTest extends TestCase
{
    #[Test]
    public function transformToResourceIdentifierWhenObjectIsNull(): void
    {
        $resource = $this->createResource();

        $resourceIdentifier = $this->toResourceIdentifier($resource, null);

        self::assertNull($resourceIdentifier);
    }

    #[Test]
    public function transformToResourceIdentifierWhenObjectIsNotNull(): void
    {
        $resource = $this->createResource('user', '1');

        $resourceIdentifier = $this->toResourceIdentifier($resource, []);

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
            ],
            $resourceIdentifier,
        );
    }

    #[Test]
    public function transformToResourceIdentifierWithMeta(): void
    {
        $resource = $this->createResource('user', '1', ['abc' => 'def']);

        $resourceIdentifier = $this->toResourceIdentifier($resource, []);

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
                'meta' => ['abc' => 'def'],
            ],
            $resourceIdentifier,
        );
    }

    #[Test]
    public function transformToResourceObjectWhenNull(): void
    {
        $resource = $this->createResource('user', '1');

        $resourceObject = $this->toResourceObject($resource, null);

        self::assertNull($resourceObject);
    }

    #[Test]
    public function transformToResourceObjectWhenAlmostEmpty(): void
    {
        $resource = $this->createResource('user', '1');

        $resourceObject = $this->toResourceObject($resource, []);

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithMeta(): void
    {
        $resource = $this->createResource('', '', ['abc' => 'def']);

        $resourceObject = $this->toResourceObject($resource, []);

        self::assertSame(
            [
                'type' => '',
                'id' => '',
                'meta' => ['abc' => 'def'],
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithLinks(): void
    {
        $resource = $this->createResource('', '', [], new ResourceLinks());

        $resourceObject = $this->toResourceObject($resource, []);

        self::assertSame(
            [
                'type' => '',
                'id' => '',
                'links' => [],
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithMetaAndLinks(): void
    {
        $resource = $this->createResource('user', '1', ['abc' => 'def'], new ResourceLinks());

        $resourceObject = $this->toResourceObject($resource, []);

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
                'meta' => ['abc' => 'def'],
                'links' => [],
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithAttributes(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            ['abc' => 'def'],
            new ResourceLinks(),
            [
                'full_name' => static fn (array $object, JsonApiRequestInterface $request) => $object['name'],
                'birth' => static fn (array $object) => 2015 - $object['age'],
            ],
        );

        $resourceObject = $this->toResourceObject(
            $resource,
            [
                'name' => 'John Doe',
                'age' => '30',
            ],
        );

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
                'meta' => ['abc' => 'def'],
                'links' => [],
                'attributes' => [
                    'full_name' => 'John Doe',
                    'birth' => 1985,
                ],
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithDefaultRelationship(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            ['father'],
            [
                'father' => static function (array $object, JsonApiRequestInterface $request): ToOneRelationship {
                    return ToOneRelationship::create()
                        ->setData([''], new StubResource('user', '2'));
                },
            ],
        );

        $resourceObject = $this->toResourceObject($resource, []);

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
                'relationships' => [
                    'father' => [
                        'data' => [
                            'type' => 'user',
                            'id' => '2',
                        ],
                    ],
                ],
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithoutRelationships(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            [],
            [
                'father' => static fn (): ToOneRelationship => ToOneRelationship::create(),
            ],
        );

        $resourceObject = $this->toResourceObject($resource, [], StubJsonApiRequest::create(['fields' => ['user' => '']]));

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToResourceObjectWithInvalidRelationship(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            ['father'],
            [
                'father' => static fn (): ToOneRelationship => ToOneRelationship::create(),
            ],
        );

        $this->expectException(InclusionUnrecognized::class);

        $this->toResourceObject($resource, [], StubJsonApiRequest::create(['include' => 'mother']));
    }

    #[Test]
    public function transformToResourceObjectWithRelationships(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            [],
            [
                'father' => static function (): ToOneRelationship {
                    return ToOneRelationship::create()
                        ->setData(null, new StubResource());
                },
            ],
        );

        $resourceObject = $this->toResourceObject($resource, []);

        self::assertSame(
            [
                'type' => 'user',
                'id' => '1',
                'relationships' => [
                    'father' => [
                        'data' => null,
                    ],
                ],
            ],
            $resourceObject,
        );
    }

    #[Test]
    public function transformToRelationshipObjectWhenEmpty(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            [],
            [],
        );

        $this->expectException(RelationshipNotExists::class);

        $this->toRelationshipObject($resource, [], null, 'father');
    }

    #[Test]
    public function transformToRelationshipObjectWhenNotFound(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            [],
            [
                'father' => static function (): ToOneRelationship {
                    return ToOneRelationship::create()
                        ->setData(['Father Vader'], new StubResource('user', '2'));
                },
            ],
        );

        $this->expectException(RelationshipNotExists::class);

        $this->toRelationshipObject($resource, [], null, 'mother');
    }

    #[Test]
    public function transformToRelationshipObject(): void
    {
        $resource = $this->createResource(
            'user',
            '1',
            [],
            null,
            [],
            [],
            [
                'father' => static function (): ToOneRelationship {
                    return ToOneRelationship::create()
                        ->setData(['Father Vader'], new StubResource('user', '2'));
                },
            ],
        );

        $resourceObject = $this->toRelationshipObject($resource, [], null, 'father');

        self::assertSame(
            [
                'data' => [
                    'type' => 'user',
                    'id' => '2',
                ],
            ],
            $resourceObject,
        );
    }

    protected function createResourceTransformer(): ResourceTransformer
    {
        return new ResourceTransformer();
    }

    /**
     * @param mixed $object
     */
    private function toResourceIdentifier(
        ResourceInterface $resource,
        $object,
        ?JsonApiRequestInterface $request = null
    ): ?array {
        $transformation = new ResourceTransformation(
            $resource,
            $object,
            '',
            $request ?? new JsonApiRequest(
                new DiactorosServerRequest(),
                new DefaultExceptionFactory(),
                new JsonDeserializer(),
            ),
            '',
            '',
            '',
            new DefaultExceptionFactory(),
        );

        $transformer = new ResourceTransformer();

        return $transformer->transformToResourceIdentifier($transformation);
    }

    /**
     * @param mixed $object
     */
    private function toResourceObject(
        ResourceInterface $resource,
        $object,
        ?JsonApiRequestInterface $request = null
    ): ?array {
        $transformation = new ResourceTransformation(
            $resource,
            $object,
            '',
            $request ?? new JsonApiRequest(
                new DiactorosServerRequest(),
                new DefaultExceptionFactory(),
                new JsonDeserializer(),
            ),
            '',
            '',
            '',
            new DefaultExceptionFactory(),
        );

        $transformer = new ResourceTransformer();

        return $transformer->transformToResourceObject($transformation, new DummyData());
    }

    /**
     * @param mixed $object
     */
    private function toRelationshipObject(
        ResourceInterface $resource,
        $object,
        ?JsonApiRequestInterface $request = null,
        string $requestedRelationshipName = ''
    ): ?array {
        $transformation = new ResourceTransformation(
            $resource,
            $object,
            '',
            $request ?? new JsonApiRequest(
                new DiactorosServerRequest(),
                new DefaultExceptionFactory(),
                new JsonDeserializer(),
            ),
            '',
            $requestedRelationshipName,
            $requestedRelationshipName,
            new DefaultExceptionFactory(),
        );

        $transformer = new ResourceTransformer();

        return $transformer->transformToRelationshipObject($transformation, new DummyData());
    }

    private function createResource(
        string $type = '',
        string $id = '',
        array $meta = [],
        ?ResourceLinks $links = null,
        array $attributes = [],
        array $defaultRelationships = [],
        array $relationships = []
    ): StubResource {
        return new StubResource($type, $id, $meta, $links, $attributes, $defaultRelationships, $relationships);
    }
}
