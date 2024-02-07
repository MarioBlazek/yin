<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Hydrator;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeMissing;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractCreateHydrator implements HydratorInterface
{
    use CreateHydratorTrait;
    use HydratorTrait;

    /**
     * @throws ResourceTypeMissing|JsonApiExceptionInterface
     *
     * @see CreateHydratorTrait::hydrateForCreate()
     */
    public function hydrate(JsonApiRequestInterface $request, ExceptionFactoryInterface $exceptionFactory, mixed $domainObject): mixed
    {
        $domainObject = $this->hydrateForCreate($request, $exceptionFactory, $domainObject);

        $this->validateDomainObject($request, $exceptionFactory, $domainObject);

        return $domainObject;
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
