<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Pagination;

use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedCursorBasedPagination;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\Utils;

trait FixedCursorBasedPaginationLinkProviderTrait
{
    abstract public function getFirstItem(): mixed;

    abstract public function getLastItem(): mixed;

    abstract public function getCurrentItem(): mixed;

    abstract public function getPreviousItem(): mixed;

    abstract public function getNextItem(): mixed;

    public function getSelfLink(string $uri, string $queryString): ?Link
    {
        if ($this->getCurrentItem() === null) {
            return null;
        }

        return $this->createPaginatedLink($uri, $queryString, $this->getCurrentItem());
    }

    public function getFirstLink(string $uri, string $queryString): ?Link
    {
        return $this->createPaginatedLink($uri, $queryString, $this->getFirstItem());
    }

    public function getLastLink(string $uri, string $queryString): ?Link
    {
        return $this->createPaginatedLink($uri, $queryString, $this->getLastItem());
    }

    public function getPrevLink(string $uri, string $queryString): ?Link
    {
        return $this->createPaginatedLink($uri, $queryString, $this->getPreviousItem());
    }

    public function getNextLink(string $uri, string $queryString): ?Link
    {
        return $this->createPaginatedLink($uri, $queryString, $this->getNextItem());
    }

    protected function createPaginatedLink(string $uri, string $queryString, mixed $cursor): ?Link
    {
        if ($cursor === null) {
            return null;
        }

        return new Link(
            Utils::getUri($uri, $queryString, FixedCursorBasedPagination::getPaginationQueryParams($cursor)),
        );
    }
}
