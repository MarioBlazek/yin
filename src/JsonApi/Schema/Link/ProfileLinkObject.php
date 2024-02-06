<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Link;

class ProfileLinkObject extends LinkObject
{
    public function __construct(string $href, array $meta = [], /**
     * @var string[]
     */
        private array $aliases = [])
    {
        parent::__construct($href);
        $this->meta = $meta;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getAlias(string $keyword): string
    {
        return $this->aliases[$keyword] ?? '';
    }

    public function addAlias(string $keyword, string $alias): self
    {
        $this->aliases[$keyword] = $alias;

        return $this;
    }

    /**
     * @internal
     *
     * @return array|mixed
     */
    public function transform(string $baseUri)
    {
        $link = parent::transform($baseUri);

        foreach ($this->aliases as $keyword => $alias) {
            $link['aliases'][$keyword] = $alias;
        }

        return $link;
    }
}
