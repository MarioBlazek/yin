<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Data;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Data\SingleResourceData;

class SingleResourceDataTest extends TestCase
{
    #[Test]
    public function getNonExistentResource(): void
    {
        $resources = [
            [
                'type' => 'resource',
                'id' => '1',
            ],
        ];

        $data = $this->createData()->setIncludedResources($resources);
        self::assertNull($data->getResource('resource', '2'));
        self::assertNull($data->getResource('resources', '1'));
    }

    #[Test]
    public function getResource(): void
    {
        $resource = [
            'type' => 'resource',
            'id' => '1',
        ];

        $data = $this->createData()->addIncludedResource($resource);
        self::assertSame($resource, $data->getResource('resource', '1'));
    }

    #[Test]
    public function isEmptyByDefault(): void
    {
        $included = $this->createData();
        self::assertFalse($included->hasIncludedResources());
    }

    #[Test]
    public function isEmptyWhenIncludingNoResource(): void
    {
        $resources = [
            [
                'type' => 'resource',
                'id' => '1',
            ],
        ];

        $data = $this->createData()->setIncludedResources($resources);
        self::assertTrue($data->hasIncludedResources());
    }

    #[Test]
    public function isEmptyWhenIncludingResources(): void
    {
        $resources = [];

        $data = $this->createData()->setIncludedResources($resources);
        self::assertFalse($data->hasIncludedResources());
    }

    #[Test]
    public function addResource(): void
    {
        $resource = [
            'type' => 'resource',
            'id' => '1',
        ];

        $data = $this->createData()->addIncludedResource($resource);
        self::assertSame($resource, $data->getResource('resource', '1'));
    }

    #[Test]
    public function transformEmpty(): void
    {
        $data = $this->createData();

        self::assertSame([], $data->transformIncluded());
    }

    #[Test]
    public function transform(): void
    {
        $data = $this->createData()->setIncludedResources([
            ['type' => 'item', 'id' => '1'],
            ['type' => 'resource', 'id' => '2'],
            ['type' => 'resource', 'id' => '1'],
            ['type' => 'item', 'id' => '2'],
            ['type' => 'item', 'id' => '1'],
            ['type' => 'resource', 'id' => '2'],
        ]);

        self::assertSame(
            [
                ['type' => 'item', 'id' => '1'],
                ['type' => 'resource', 'id' => '2'],
                ['type' => 'resource', 'id' => '1'],
                ['type' => 'item', 'id' => '2'],
            ],
            $data->transformIncluded(),
        );
    }

    #[Test]
    public function transformSinglePrimaryResources(): void
    {
        $data = $this->createData();

        $data->addPrimaryResource(['type' => 'user', 'id' => '1']);

        self::assertSame(['type' => 'user', 'id' => '1'], $data->transformPrimaryData());
    }

    #[Test]
    public function transformMultiplePrimaryResources(): void
    {
        $data = $this->createData();

        $data->setPrimaryResources(
            [
                ['type' => 'user', 'id' => '1'],
                ['type' => 'user', 'id' => '2'],
                ['type' => 'dog', 'id' => '4'],
                ['type' => 'dog', 'id' => '3'],
                ['type' => 'user', 'id' => '3'],
            ],
        );

        self::assertSame(['type' => 'user', 'id' => '1'], $data->transformPrimaryData());
    }

    private function createData(): SingleResourceData
    {
        return new SingleResourceData();
    }
}
