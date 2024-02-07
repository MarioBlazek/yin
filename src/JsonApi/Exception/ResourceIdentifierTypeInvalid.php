<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;

class ResourceIdentifierTypeInvalid extends AbstractJsonApiException
{
    protected string $type;

    public function __construct(string $type)
    {
        parent::__construct("The resource type must be a string instead of {$type}!", 400);
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('400')
                ->setCode('RESOURCE_IDENTIFIER_TYPE_INVALID')
                ->setTitle('Resource identifier type is invalid')
                ->setDetail("The resource type must be a string instead of {$this->type}!"),
        ];
    }
}
