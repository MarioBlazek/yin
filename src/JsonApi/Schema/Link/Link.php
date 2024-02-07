<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Link;

class Link
{
    public function __construct(private readonly string $href) {}

    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @internal
     */
    public function transform(string $baseUri): mixed
    {
        return $baseUri . $this->href;
    }
}
