<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Hydrator;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipNotExists;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeMissing;
use WoohooLabs\Yin\JsonApi\Exception\ResourceTypeUnacceptable;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

use function is_string;

trait UpdateHydratorTrait
{
    /**
     * Hydrates the domain object from the updating request.
     *
     * The domain object's attributes and relationships are hydrated
     * according to the JSON:API specification.
     *
     * @throws JsonApiExceptionInterface
     */
    public function hydrateForUpdate(
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        mixed $domainObject
    ): mixed
    {
        $data = $request->getResource();
        if ($data === null) {
            throw $exceptionFactory->createDataMemberMissingException($request);
        }

        $this->validateType($data, $exceptionFactory);
        $domainObject = $this->hydrateIdForUpdate($domainObject, $data, $exceptionFactory);
        $this->validateRequest($request);
        $domainObject = $this->hydrateAttributes($domainObject, $data);

        return $this->hydrateRelationships($domainObject, $data, $exceptionFactory);
    }

    /**
     * @throws RelationshipNotExists|JsonApiExceptionInterface
     */
    public function hydrateForRelationshipUpdate(
        string $relationship,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        mixed $domainObject
    ): mixed {
        $relationshipHydrators = $this->getRelationshipHydrator($domainObject);

        if (isset($relationshipHydrators[$relationship]) === false) {
            throw $exceptionFactory->createRelationshipNotExistsException($relationship);
        }

        $relationshipHydrator = $relationshipHydrators[$relationship];

        $body = $request->getParsedBody();

        return $this->doHydrateRelationship(
            $domainObject,
            $relationship,
            $relationshipHydrator,
            $exceptionFactory,
            $body !== null ? (array) $body : null,
            $request->getResource(),
        );
    }

    /**
     * @throws ResourceTypeMissing|JsonApiExceptionInterface
     * @throws ResourceTypeUnacceptable|JsonApiExceptionInterface
     */
    abstract protected function validateType(array $data, ExceptionFactoryInterface $exceptionFactory): void;

    /**
     * You can validate the request.
     *
     * @throws JsonApiExceptionInterface
     */
    abstract protected function validateRequest(JsonApiRequestInterface $request): void;

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

    /**
     * Provides the relationship hydrators.
     *
     * The method returns an array of relationship hydrators, where a hydrator is a key-value pair:
     * the key is the specific relationship name which comes from the request and the value is an
     * callable which hydrate the previous relationship.
     * These callables receive the domain object (which will be hydrated), an object representing the
     * currently processed relationship (it can be a ToOneRelationship or a ToManyRelationship
     * object), the "data" part of the request and the relationship name as their arguments, and
     * they should mutate the state of the domain object.
     * If it is an immutable object or an array (and passing by reference isn't used),
     * the callable should return the domain object.
     *
     * @return array<string, callable>
     */
    abstract protected function getRelationshipHydrator(mixed $domainObject): array;

    abstract protected function doHydrateRelationship(
        mixed $domainObject,
        string $relationshipName,
        callable $hydrator,
        ExceptionFactoryInterface $exceptionFactory,
        ?array $relationshipData,
        ?array $data
    ): mixed;

    /**
     * @throws JsonApiExceptionInterface
     */
    protected function hydrateIdForUpdate(mixed $domainObject, array $data, ExceptionFactoryInterface $exceptionFactory): mixed
    {
        if (empty($data['id'])) {
            throw $exceptionFactory->createResourceIdMissingException();
        }

        if (is_string($data['id']) === false) {
            throw $exceptionFactory->createResourceIdInvalidException($data['id']);
        }

        $result = $this->setId($domainObject, $data['id']);
        if ($result !== null) {
            $domainObject = $result;
        }

        return $domainObject;
    }
}
