<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Serializer;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Serializer\JsonSerializer;

use function json_encode;

class JsonSerializerTest extends TestCase
{
    #[Test]
    public function serializeBody(): void
    {
        $serializer = new JsonSerializer();

        $response = $serializer->serialize(
            new Response(),
            [
                'data' => [
                    'type' => 'cat',
                    'id' => 'tom',
                ],
            ],
        );

        self::assertSame(
            json_encode(
                [
                    'data' => [
                        'type' => 'cat',
                        'id' => 'tom',
                    ],
                ],
            ),
            $response->getBody()->__toString(),
        );
    }

    #[Test]
    public function getBodyAsString(): void
    {
        $response = new Response();
        $response->getBody()->write('abc');

        $serializer = new JsonSerializer();

        self::assertSame('abc', $serializer->getBodyAsString($response));
    }
}
