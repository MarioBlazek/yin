<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;

class RelationshipTypeInappropriate extends AbstractJsonApiException
{
    protected string $relationshipName;
    protected string $currentRelationshipType;
    protected string $expectedRelationshipType;

    public function __construct(
        string $relationshipName,
        string $currentRelationshipType,
        string $expectedRelationshipType
    ) {
        parent::__construct(
            "The provided relationship '{$relationshipName}' is of type of {$currentRelationshipType}, but " .
            ($expectedRelationshipType !== '' ? "{$expectedRelationshipType} is" : 'it is not the one which is') . ' expected!',
            400,
        );
        $this->relationshipName = $relationshipName;
        $this->currentRelationshipType = $currentRelationshipType;
        $this->expectedRelationshipType = $expectedRelationshipType;
    }

    public function getRelationshipName(): string
    {
        return $this->relationshipName;
    }

    public function getCurrentRelationshipType(): string
    {
        return $this->currentRelationshipType;
    }

    public function getExpectedRelationshipType(): string
    {
        return $this->expectedRelationshipType;
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('400')
                ->setCode('RELATIONSHIP_TYPE_INAPPROPRIATE')
                ->setTitle('Relationship type is inappropriate')
                ->setDetail($this->getMessage())
                ->setSource(ErrorSource::fromPointer("/data/relationships/{$this->relationshipName}")),
        ];
    }
}
