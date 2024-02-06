<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Serializer;

use InvalidArgumentException;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;

class JsonDeserializerTest extends TestCase
{
    #[Test]
    public function deserializeNullBody(): void
    {
        $request = $this->createRequestWithJsonBody(null);

        $deserializer = new JsonDeserializer();

        self::assertNull($deserializer->deserialize($request));
    }

    #[Test]
    public function deserializeEmptyBody(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createRequestWithJsonBody('');
    }

    #[Test]
    public function deserialize(): void
    {
        $parsedBody = [
            'data' => [
                'type' => 'cat',
                'id' => 'tom',
            ],
        ];

        $request = $this->createRequestWithJsonBody($parsedBody);

        self::assertSame($parsedBody, $request->getParsedBody());
    }

    /**
     * @param mixed $body
     */
    private function createRequestWithJsonBody($body): ServerRequest
    {
        $request = new ServerRequest();

        return $request->withParsedBody($body);
    }
}
