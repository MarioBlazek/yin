<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;

class ResourceIdentifierTypeMissing extends AbstractJsonApiException
{
    public function __construct(private readonly array $resourceIdentifier)
    {
        parent::__construct('A type for the resource identifier must be included!', 400);
    }

    public function getResourceIdentifier(): array
    {
        return $this->resourceIdentifier;
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('400')
                ->setCode('RESOURCE_IDENTIFIER_TYPE_MISSING')
                ->setTitle('A type for the resource identifier is missing')
                ->setDetail('A type for the resource identifier must be included!'),
        ];
    }
}
