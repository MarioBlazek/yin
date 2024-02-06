<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi;

use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\InclusionUnsupported;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\SortingUnsupported;
use WoohooLabs\Yin\JsonApi\Hydrator\HydratorInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\UpdateRelationshipHydratorInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Request\Pagination\PaginationFactory;
use WoohooLabs\Yin\JsonApi\Response\Responder;
use WoohooLabs\Yin\JsonApi\Serializer\JsonSerializer;
use WoohooLabs\Yin\JsonApi\Serializer\SerializerInterface;

class JsonApi
{
    public JsonApiRequestInterface $request;

    public ResponseInterface $response;

    public function __construct(
        JsonApiRequestInterface $request,
        ResponseInterface $response,
        protected ExceptionFactoryInterface $exceptionFactory = new DefaultExceptionFactory(),
        protected SerializerInterface $serializer = new JsonSerializer()
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): JsonApiRequestInterface
    {
        return $this->request;
    }

    public function setRequest(JsonApiRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getExceptionFactory(): ExceptionFactoryInterface
    {
        return $this->exceptionFactory;
    }

    public function setExceptionFactory(ExceptionFactoryInterface $exceptionFactory): void
    {
        $this->exceptionFactory = $exceptionFactory;
    }

    public function getPaginationFactory(): PaginationFactory
    {
        return new PaginationFactory($this->request);
    }

    public function respond(): Responder
    {
        return new Responder($this->request, $this->response, $this->exceptionFactory, $this->serializer);
    }

    public function hydrate(HydratorInterface $hydrator, mixed $object): mixed
    {
        return $hydrator->hydrate($this->request, $this->exceptionFactory, $object);
    }

    public function hydrateRelationship(
        string $relationship,
        UpdateRelationshipHydratorInterface $hydrator,
        mixed $object
    ): mixed {
        return $hydrator->hydrateRelationship($relationship, $this->request, $this->exceptionFactory, $object);
    }

    /**
     * Disables inclusion of related resources.
     *
     * If the current request asks for inclusion of related resources, it throws an InclusionNotSupported exception.
     *
     * @throws InclusionUnsupported|JsonApiExceptionInterface
     */
    public function disableIncludes(): void
    {
        if ($this->request->getQueryParam('include') !== null) {
            throw $this->exceptionFactory->createInclusionUnsupportedException($this->request);
        }
    }

    /**
     * Disables sorting.
     *
     * If the current request contains sorting criteria, it throws a SortingNotSupported exception.
     *
     * @throws SortingUnsupported|JsonApiExceptionInterface
     */
    public function disableSorting(): void
    {
        if ($this->request->getQueryParam('sort') !== null) {
            throw $this->exceptionFactory->createSortingUnsupportedException($this->request);
        }
    }
}
