<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Link;

class ResourceLinks extends AbstractLinks
{
    public function __construct(string $baseUri = '', ?Link $self = null)
    {
        parent::__construct($baseUri, ['self' => $self]);
    }

    public static function createWithoutBaseUri(?Link $self = null): self
    {
        return new self('', $self);
    }

    public static function createWithBaseUri(string $baseUri, ?Link $self = null): self
    {
        return new self($baseUri, $self);
    }

    public function setBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function getSelf(): ?Link
    {
        return $this->getLink('self');
    }

    public function setSelf(?Link $self): self
    {
        $this->addLink('self', $self);

        return $this;
    }
}
