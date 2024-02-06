<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Transformer;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;

/**
 * @internal
 */
final class ResourceTransformation
{
    /**
     * @var ResourceInterface|null
     */
    public $resource;

    /**
     * @var string
     */
    public $resourceType;

    /**
     * @var JsonApiRequestInterface
     */
    public $request;

    /**
     * @var string
     */
    public $basePath;

    /**
     * @var string
     */
    public $requestedRelationshipName;

    /**
     * @var string
     */
    public $currentRelationshipName;

    /**
     * @var ExceptionFactoryInterface
     */
    public $exceptionFactory;

    /**
     * @var array|null
     */
    public $result;

    public function __construct(
        ?ResourceInterface $resource,
        public mixed $object,
        string $resourceType,
        JsonApiRequestInterface $request,
        string $basePath,
        string $requestedRelationshipName,
        string $currentRelationshipName,
        ExceptionFactoryInterface $exceptionFactory
    ) {
        $this->resource = $resource;
        $this->resourceType = $resourceType;
        $this->request = $request;
        $this->basePath = $basePath;
        $this->requestedRelationshipName = $requestedRelationshipName;
        $this->currentRelationshipName = $currentRelationshipName;
        $this->exceptionFactory = $exceptionFactory;
    }
}
