<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Hydrator;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipNotExists;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeMissing;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractUpdateHydrator implements HydratorInterface, UpdateRelationshipHydratorInterface
{
    use HydratorTrait;
    use UpdateHydratorTrait;

    /**
     * Alias for UpdateHydratorTrait::hydrateForUpdate().
     *
     * @see UpdateHydratorTrait::hydrateForUpdate()
     *
     * @throws ResourceTypeMissing|JsonApiExceptionInterface
     */
    public function hydrate(JsonApiRequestInterface $request, ExceptionFactoryInterface $exceptionFactory, mixed $domainObject): mixed
    {
        $domainObject = $this->hydrateForUpdate($request, $exceptionFactory, $domainObject);

        $this->validateDomainObject($request, $exceptionFactory, $domainObject);

        return $domainObject;
    }

    /**
     * @throws RelationshipNotExists|JsonApiExceptionInterface
     */
    public function hydrateRelationship(
        string $relationship,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        object $domainObject
    ): mixed {
        return $this->hydrateForRelationshipUpdate($relationship, $request, $exceptionFactory, $domainObject);
    }

    /**
     * You can validate the domain object after it has been hydrated from the request.
     */
    protected function validateDomainObject(
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        mixed $domainObject
    ): void {}
}
