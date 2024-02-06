<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;

class SortParamUnrecognized extends AbstractJsonApiException
{
    /**
     * @var string
     */
    protected $sortParam;

    public function __construct(string $sortParam)
    {
        parent::__construct("Sorting parameter '{$sortParam}' , can't be recognized!", 400);
        $this->sortParam = $sortParam;
    }

    public function getSortParam(): string
    {
        return $this->sortParam;
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('400')
                ->setCode('SORTING_UNRECOGNIZED')
                ->setTitle('Sorting paramter is unrecognized')
                ->setDetail("Sorting parameter '{$this->sortParam}' can't be recognized by the endpoint!")
                ->setSource(ErrorSource::fromParameter('sort')),
        ];
    }
}
