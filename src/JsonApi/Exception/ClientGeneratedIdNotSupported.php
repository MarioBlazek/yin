<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;

class ClientGeneratedIdNotSupported extends AbstractJsonApiException
{
    protected string $clientGeneratedId;

    public function __construct(string $clientGeneratedId)
    {
        parent::__construct(
            'Client generated ID ' . ($clientGeneratedId !== '' ? "'{$clientGeneratedId}' " : '') .
            'is not supported!',
            403,
        );
        $this->clientGeneratedId = $clientGeneratedId;
    }

    public function getClientGeneratedId(): string
    {
        return $this->clientGeneratedId;
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('403')
                ->setCode('CLIENT_GENERATED_ID_NOT_SUPPORTED')
                ->setTitle('Client generated ID is not supported')
                ->setDetail($this->getMessage())
                ->setSource(ErrorSource::fromPointer('/data/id')),
        ];
    }
}
