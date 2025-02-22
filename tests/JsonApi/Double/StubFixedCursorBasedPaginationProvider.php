<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Double;

use WoohooLabs\Yin\JsonApi\Schema\Pagination\FixedCursorBasedPaginationLinkProviderTrait;

class StubFixedCursorBasedPaginationProvider
{
    use FixedCursorBasedPaginationLinkProviderTrait;

    /**
     * @var mixed
     */
    private $firstItem;

    /**
     * @var mixed
     */
    private $lastItem;

    /**
     * @var mixed
     */
    private $currentItem;

    /**
     * @var mixed
     */
    private $previousItem;

    /**
     * @var mixed
     */
    private $nextItem;

    /**
     * @param mixed $firstItem
     * @param mixed $lastItem
     * @param mixed $currentItem
     * @param mixed $previousItem
     * @param mixed $nextItem
     */
    public function __construct($firstItem, $lastItem, $currentItem, $previousItem, $nextItem)
    {
        $this->firstItem = $firstItem;
        $this->lastItem = $lastItem;
        $this->currentItem = $currentItem;
        $this->previousItem = $previousItem;
        $this->nextItem = $nextItem;
    }

    /**
     * @return mixed
     */
    public function getFirstItem(): mixed
    {
        return $this->firstItem;
    }

    /**
     * @return mixed
     */
    public function getLastItem(): mixed
    {
        return $this->lastItem;
    }

    /**
     * @return mixed
     */
    public function getCurrentItem(): mixed
    {
        return $this->currentItem;
    }

    /**
     * @return mixed
     */
    public function getPreviousItem(): mixed
    {
        return $this->previousItem;
    }

    /**
     * @return mixed
     */
    public function getNextItem(): mixed
    {
        return $this->nextItem;
    }
}
