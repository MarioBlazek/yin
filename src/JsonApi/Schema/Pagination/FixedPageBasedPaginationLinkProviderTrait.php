<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Pagination;

use WoohooLabs\Yin\JsonApi\Request\Pagination\FixedPageBasedPagination;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\Utils;

use function ceil;

trait FixedPageBasedPaginationLinkProviderTrait
{
    abstract public function getTotalItems(): int;

    abstract public function getPage(): int;

    abstract public function getSize(): int;

    public function getSelfLink(string $uri, string $queryString): ?Link
    {
        if ($this->getPage() <= 0 || $this->getSize() <= 0 || $this->getPage() > $this->getLastPage()) {
            return null;
        }

        return $this->createPaginatedLink($uri, $queryString, $this->getPage());
    }

    public function getFirstLink(string $uri, string $queryString): ?Link
    {
        return $this->createPaginatedLink($uri, $queryString, 1);
    }

    public function getLastLink(string $uri, string $queryString): ?Link
    {
        if ($this->getSize() <= 0) {
            return null;
        }

        return $this->createPaginatedLink($uri, $queryString, $this->getLastPage());
    }

    public function getPrevLink(string $uri, string $queryString): ?Link
    {
        if ($this->getPage() <= 1 || $this->getSize() <= 0) {
            return null;
        }

        return $this->createPaginatedLink($uri, $queryString, $this->getPage() - 1);
    }

    public function getNextLink(string $uri, string $queryString): ?Link
    {
        if ($this->getPage() <= 0 || $this->getSize() <= 0 || $this->getPage() >= $this->getLastPage()) {
            return null;
        }

        return $this->createPaginatedLink($uri, $queryString, $this->getPage() + 1);
    }

    protected function createPaginatedLink(string $uri, string $queryString, int $page): ?Link
    {
        if ($this->getTotalItems() <= 0 || $this->getSize() <= 0) {
            return null;
        }

        return new Link(
            Utils::getUri($uri, $queryString, FixedPageBasedPagination::getPaginationQueryParams($page)),
        );
    }

    protected function getLastPage(): int
    {
        return (int) ceil($this->getTotalItems() / $this->getSize());
    }
}
