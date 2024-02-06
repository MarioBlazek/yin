<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Link;

use WoohooLabs\Yin\JsonApi\Schema\Pagination\PaginationLinkProviderInterface;

use function array_values;

class DocumentLinks extends AbstractLinks
{
    /**
     * @var Link[]
     */
    protected $profiles = [];

    /**
     * @param Link[] $links
     * @param Link[] $profiles
     */
    public function __construct(string $baseUri = '', array $links = [], array $profiles = [])
    {
        parent::__construct($baseUri, $links);
        foreach ($profiles as $profile) {
            $this->addProfile($profile);
        }
    }

    /**
     * @param Link[] $links
     * @param Link[] $profiles
     */
    public static function createWithoutBaseUri(array $links = [], array $profiles = []): self
    {
        return new self('', $links, $profiles);
    }

    /**
     * @param Link[] $links
     * @param Link[] $profiles
     */
    public static function createWithBaseUri(string $baseUri, array $links = [], array $profiles = []): self
    {
        return new self($baseUri, $links, $profiles);
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

    public function getFirst(): ?Link
    {
        return $this->getLink('first');
    }

    public function setFirst(?Link $first): self
    {
        $this->addLink('first', $first);

        return $this;
    }

    public function getLast(): ?Link
    {
        return $this->getLink('last');
    }

    public function setLast(?Link $last): self
    {
        $this->addLink('last', $last);

        return $this;
    }

    public function getPrev(): ?Link
    {
        return $this->getLink('prev');
    }

    public function setPrev(?Link $prev): self
    {
        $this->addLink('prev', $prev);

        return $this;
    }

    public function getNext(): ?Link
    {
        return $this->getLink('next');
    }

    public function setNext(?Link $next): self
    {
        $this->addLink('next', $next);

        return $this;
    }

    /**
     * @return Link[]
     */
    public function getProfiles(): array
    {
        return array_values($this->profiles);
    }

    public function addProfile(Link $profile): self
    {
        $this->profiles[$profile->getHref()] = $profile;

        return $this;
    }

    public function setPagination(string $uri, PaginationLinkProviderInterface $paginationProvider, string $queryString = ''): self
    {
        $this->setSelf($paginationProvider->getSelfLink($uri, $queryString));
        $this->setFirst($paginationProvider->getFirstLink($uri, $queryString));
        $this->setLast($paginationProvider->getLastLink($uri, $queryString));
        $this->setPrev($paginationProvider->getPrevLink($uri, $queryString));
        $this->setNext($paginationProvider->getNextLink($uri, $queryString));

        return $this;
    }
}
