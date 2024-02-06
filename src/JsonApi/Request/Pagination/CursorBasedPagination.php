<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Request\Pagination;

use WoohooLabs\Yin\Utils;

use function http_build_query;

class CursorBasedPagination
{
    /**
     * @var int
     */
    protected $size;

    public function __construct(protected mixed $cursor, int $size = 0)
    {
        $this->size = $size;
    }

    public static function fromPaginationQueryParams(array $paginationQueryParams, mixed $defaultCursor = null, int $defaultSize = 0): self
    {
        return new self(
            $paginationQueryParams['cursor'] ?? $defaultCursor,
            Utils::getIntegerFromQueryParam($paginationQueryParams, 'size', $defaultSize),
        );
    }

    /**
     * @return mixed
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public static function getPaginationQueryString(mixed $cursor, int $size): string
    {
        return http_build_query(static::getPaginationQueryParams($cursor, $size));
    }

    public static function getPaginationQueryParams(mixed $cursor, int $size): array
    {
        return [
            'page' => [
                'cursor' => $cursor,
                'size' => $size,
            ],
        ];
    }
}
