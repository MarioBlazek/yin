<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Data;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Data\CollectionData;

class CollectionDataTest extends TestCase
{
    #[Test]
    public function transformSinglePrimaryResourcesInTheOrderDefined(): void
    {
        $data = new CollectionData();
        $data->addPrimaryResource(['type' => 'user', 'id' => '1']);

        self::assertSame(
            [
                ['type' => 'user', 'id' => '1'],
            ],
            $data->transformPrimaryData(),
        );
    }

    #[Test]
    public function transformMultiplePrimaryResourcesInTheOrderDefined(): void
    {
        $data = new CollectionData();
        $data->setPrimaryResources(
            [
                ['type' => 'user', 'id' => '1'],
                ['type' => 'user', 'id' => '2'],
                ['type' => 'dog', 'id' => '4'],
                ['type' => 'dog', 'id' => '3'],
                ['type' => 'user', 'id' => '3'],
            ],
        );

        self::assertSame(
            [
                ['type' => 'user', 'id' => '1'],
                ['type' => 'user', 'id' => '2'],
                ['type' => 'dog', 'id' => '4'],
                ['type' => 'dog', 'id' => '3'],
                ['type' => 'user', 'id' => '3'],
            ],
            $data->transformPrimaryData(),
        );
    }
}
