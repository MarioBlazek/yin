<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Hydrator;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeMissing;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeUnacceptable;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

use function is_string;

trait CreateHydratorTrait
{
    /**
     * Hydrates the domain object from the creating request.
     *
     * The domain object's attributes and relationships are hydrated
     * according to the JSON:API specification.
     *
     * @throws JsonApiExceptionInterface
     */
    public function hydrateForCreate(
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        mixed $domainObject
    ): mixed {
        $data = $request->getResource();
        if ($data === null) {
            throw $exceptionFactory->createDataMemberMissingException($request);
        }

        $this->validateType($data, $exceptionFactory);
        $domainObject = $this->hydrateIdForCreate($domainObject, $data, $request, $exceptionFactory);
        $this->validateRequest($request);
        $domainObject = $this->hydrateAttributes($domainObject, $data);

        return $this->hydrateRelationships($domainObject, $data, $exceptionFactory);
    }

    /**
     * @throws ResourceTypeMissing|JsonApiExceptionInterface
     * @throws ResourceTypeUnacceptable|JsonApiExceptionInterface
     */
    abstract protected function validateType(array $data, ExceptionFactoryInterface $exceptionFactory): void;

    /**
     * Validates a client-generated ID.
     *
     * If the $clientGeneratedId is not a valid ID for the domain object, then
     * the appropriate exception should be thrown: if it is not well-formed then
     * a ClientGeneratedIdNotSupported exception can be raised, if the ID already
     * exists then a ClientGeneratedIdAlreadyExists exception can be thrown.
     *
     * @throws JsonApiExceptionInterface|JsonApiExceptionInterface
     */
    abstract protected function validateClientGeneratedId(
        string $clientGeneratedId,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory
    ): void;

    /**
     * You can validate the request.
     *
     * @throws JsonApiExceptionInterface
     */
    abstract protected function validateRequest(JsonApiRequestInterface $request): void;

    /**
     * Produces a new ID for the domain objects.
     *
     * UUID-s are preferred according to the JSON:API specification.
     *
     * @return string
     */
    abstract protected function generateId(): string;

    /**
     * Sets the given ID for the domain object.
     *
     * The method mutates the domain object and sets the given ID for it.
     * If it is an immutable object or an array the whole, updated domain
     * object can be returned.
     *
     * @return mixed|void
     */
    abstract protected function setId(mixed $domainObject, string $id);

    abstract protected function hydrateAttributes(mixed $domainObject, array $data): mixed;

    abstract protected function hydrateRelationships(
        mixed $domainObject,
        array $data,
        ExceptionFactoryInterface $exceptionFactory
    ): mixed;

    protected function hydrateIdForCreate(
        mixed $domainObject,
        array $data,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory
    ): mixed {
        if (empty($data['id']) === false && is_string($data['id']) === false) {
            throw $exceptionFactory->createResourceIdInvalidException($data['id']);
        }

        $id = empty($data['id']) ? '' : $data['id'];
        $this->validateClientGeneratedId($id, $request, $exceptionFactory);

        if ($id === '') {
            $id = $this->generateId();
        }

        $result = $this->setId($domainObject, $id);
        if ($result !== null) {
            $domainObject = $result;
        }

        return $domainObject;
    }
}
