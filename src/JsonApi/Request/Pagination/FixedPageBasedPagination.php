<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Request\Pagination;

use WoohooLabs\Yin\Utils;

use function http_build_query;

class FixedPageBasedPagination
{
    /**
     * @var int
     */
    protected $page;

    public function __construct(int $page)
    {
        $this->page = $page;
    }

    public static function fromPaginationQueryParams(array $paginationQueryParams, int $defaultPage = 0): self
    {
        return new self(
            Utils::getIntegerFromQueryParam($paginationQueryParams, 'number', $defaultPage),
        );
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public static function getPaginationQueryString(int $page): string
    {
        return http_build_query(static::getPaginationQueryParams($page));
    }

    public static function getPaginationQueryParams(int $page): array
    {
        return [
            'page' => [
                'number' => $page,
            ],
        ];
    }
}
