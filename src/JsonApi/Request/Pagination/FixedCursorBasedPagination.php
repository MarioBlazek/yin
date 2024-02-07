<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Request\Pagination;

use function http_build_query;

class FixedCursorBasedPagination
{
    public function __construct(protected mixed $cursor) {}

    public static function fromPaginationQueryParams(array $paginationQueryParams, mixed $defaultCursor = null): self
    {
        return new self(
            $paginationQueryParams['cursor'] ?? $defaultCursor,
        );
    }

    public function getCursor(): mixed
    {
        return $this->cursor;
    }

    public static function getPaginationQueryString(mixed $cursor): string
    {
        return http_build_query(static::getPaginationQueryParams($cursor));
    }

    public static function getPaginationQueryParams(mixed $cursor): array
    {
        return [
            'page' => [
                'cursor' => $cursor,
            ],
        ];
    }
}
