<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Data;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\Tests\JsonApi\Double\DummyData;

class AbstractDataTest extends TestCase
{
    #[Test]
    public function setPrimaryResources(): void
    {
        $dummyData = new DummyData();
        $dummyData->setPrimaryResources(
            [
                ['type' => 'user', 'id' => '1'],
                ['type' => 'user', 'id' => '2'],
            ],
        );

        self::assertTrue($dummyData->hasPrimaryResource('user', '1'));
        self::assertTrue($dummyData->hasPrimaryResource('user', '2'));
    }

    #[Test]
    public function addNotYetIncludedPrimaryResource(): void
    {
        $dummyData = new DummyData();
        $dummyData->addPrimaryResource(['type' => 'user', 'id' => '1']);

        self::assertTrue($dummyData->hasPrimaryResource('user', '1'));
    }

    #[Test]
    public function addAlreadyIncludedPrimaryResource(): void
    {
        $dummyData = new DummyData();
        $dummyData->addIncludedResource(['type' => 'user', 'id' => '1']);
        $dummyData->addPrimaryResource(['type' => 'user', 'id' => '1']);

        self::assertFalse($dummyData->hasIncludedResource('user', '1'));
        self::assertTrue($dummyData->hasPrimaryResource('user', '1'));
    }
}
