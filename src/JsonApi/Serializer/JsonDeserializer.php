<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Serializer;

use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

class JsonDeserializer implements DeserializerInterface
{
    /**
     * @param positive-int $depth
     */
    public function __construct(private readonly int $options = 0, private readonly int $depth = 512) {}

    /**
     * @return array|mixed|null
     */
    public function deserialize(ServerRequestInterface $request): mixed
    {
        return json_decode($request->getBody()->__toString(), true, $this->depth, $this->options);
    }
}
