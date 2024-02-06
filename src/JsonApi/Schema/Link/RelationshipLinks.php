<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Link;

class RelationshipLinks extends AbstractLinks
{
    public function __construct(string $baseUri = '', ?Link $self = null, ?Link $related = null)
    {
        parent::__construct($baseUri, ['self' => $self, 'related' => $related]);
    }

    public static function createWithoutBaseUri(?Link $self = null, ?Link $related = null): self
    {
        return new self('', $self, $related);
    }

    public static function createWithBaseUri(string $baseUri, ?Link $self = null, ?Link $related = null): self
    {
        return new self($baseUri, $self, $related);
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

    public function getRelated(): ?Link
    {
        return $this->getLink('related');
    }

    public function setRelated(?Link $related): self
    {
        $this->addLink('related', $related);

        return $this;
    }
}
