<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;

class QueryParamMalformed extends AbstractJsonApiException
{
    /**
     * @var string
     */
    protected $malformedQueryParam;

    public function __construct(string $malformedQueryParam, protected mixed $malformedQueryParamValue)
    {
        parent::__construct("Query parameter '{$malformedQueryParam}' is malformed!", 400);
        $this->malformedQueryParam = $malformedQueryParam;
    }

    public function getMalformedQueryParam(): string
    {
        return $this->malformedQueryParam;
    }

    /**
     * @return mixed
     */
    public function getMalformedQueryParamValue()
    {
        return $this->malformedQueryParamValue;
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('400')
                ->setCode('QUERY_PARAM_MALFORMED')
                ->setTitle('Query parameter is malformed')
                ->setDetail("Query parameter '{$this->malformedQueryParam}' is malformed!")
                ->setSource(ErrorSource::fromParameter($this->malformedQueryParam)),
        ];
    }
}
