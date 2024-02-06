<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;

class JsonApiObjectTest extends TestCase
{
    #[Test]
    public function getVersion(): void
    {
        $jsonApi = $this->createJsonApiObject('1.0');

        $version = $jsonApi->getVersion();

        self::assertSame('1.0', $version);
    }

    #[Test]
    public function getMeta(): void
    {
        $jsonApi = $this->createJsonApiObject('', ['abc' => 'def']);

        $meta = $jsonApi->getMeta();

        self::assertSame(['abc' => 'def'], $meta);
    }

    #[Test]
    public function setMeta(): void
    {
        $jsonApi = $this->createJsonApiObject('')
            ->setMeta(['abc' => 'def']);

        $meta = $jsonApi->getMeta();

        self::assertSame(['abc' => 'def'], $meta);
    }

    #[Test]
    public function transformWithEmptyVersion(): void
    {
        $jsonApi = $this->createJsonApiObject('', ['abc' => 'def']);

        $jsonApiObject = $jsonApi->transform();

        self::assertSame(
            [
                'meta' => ['abc' => 'def'],
            ],
            $jsonApiObject,
        );
    }

    #[Test]
    public function transformWithEmptyMeta(): void
    {
        $jsonApi = $this->createJsonApiObject('1.0', ['abc' => 'def']);

        $jsonApiObject = $jsonApi->transform();

        self::assertSame(
            [
                'version' => '1.0',
                'meta' => ['abc' => 'def'],
            ],
            $jsonApiObject,
        );
    }

    private function createJsonApiObject(string $version, array $meta = []): JsonApiObject
    {
        return new JsonApiObject($version, $meta);
    }
}
