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
     *
     * @return string|mixed
     */
    public function transform(string $baseUri)
    {
        return $baseUri . $this->href;
    }
}
