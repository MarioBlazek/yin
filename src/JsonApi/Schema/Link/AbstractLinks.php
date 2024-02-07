<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Link;

abstract class AbstractLinks
{
    protected string $baseUri;

    /**
     * @var Link[]|null[]
     */
    protected array $links = [];

    /**
     * @param Link[]|null[] $links
     */
    public function __construct(string $baseUri = '', array $links = [])
    {
        $this->baseUri = $baseUri;

        foreach ($links as $name => $link) {
            if ($link !== null) {
                $this->addLink($name, $link);
            }
        }
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function getLink(string $name): ?Link
    {
        return $this->links[$name] ?? null;
    }

    /**
     * @internal
     */
    public function transform(): array
    {
        $links = [];

        foreach ($this->links as $rel => $link) {
            $links[$rel] = $link !== null ? $link->transform($this->baseUri) : null;
        }

        return $links;
    }

    /**
     * @param Link[] $links
     */
    public function setLinks(array $links): static
    {
        foreach ($links as $rel => $link) {
            $this->addLink($rel, $link);
        }

        return $this;
    }

    public function setLink(string $name, ?Link $link): static
    {
        $this->addLink($name, $link);

        return $this;
    }

    protected function addLink(string $name, ?Link $link): void
    {
        $this->links[$name] = $link;
    }
}
