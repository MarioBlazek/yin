<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Double;

use LogicException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\UpdateHydratorTrait;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

class StubUpdateHydrator
{
    use UpdateHydratorTrait;

    private bool $validationException;

    public function __construct(bool $validationException = false)
    {
        $this->validationException = $validationException;
    }

    protected function validateType(array $data, ExceptionFactoryInterface $exceptionFactory): void {}

    /**
     * @return mixed|void
     */
    protected function setId(mixed $domainObject, string $id)
    {
        $domainObject['id'] = $id;

        return $domainObject;
    }

    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        if ($this->validationException) {
            throw new LogicException();
        }
    }

    /**
     * @param mixed $domainObject
     *
     * @return mixed
     */
    protected function hydrateAttributes(mixed $domainObject, array $data): mixed
    {
        return $domainObject;
    }

    /**
     * @param mixed $domainObject
     *
     * @return mixed
     */
    protected function hydrateRelationships($domainObject, array $data, ExceptionFactoryInterface $exceptionFactory): mixed
    {
        return $domainObject;
    }

    /**
     * @param mixed $domainObject
     */
    protected function getRelationshipHydrator($domainObject): array
    {
        return [];
    }

    /**
     * @param mixed $domainObject
     *
     * @return mixed
     */
    protected function doHydrateRelationship(
        $domainObject,
        string $relationshipName,
        callable $hydrator,
        ExceptionFactoryInterface $exceptionFactory,
        ?array $relationshipData,
        ?array $data
    ): mixed
    {
        return $domainObject;
    }
}
