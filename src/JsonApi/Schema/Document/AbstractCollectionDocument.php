<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Document;

use WoohooLabs\Yin\JsonApi\Schema\Data\CollectionData;
use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceDocumentTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;

abstract class AbstractCollectionDocument extends AbstractResourceDocument
{
    protected ResourceInterface $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function getResource(): ResourceInterface
    {
        return $this->resource;
    }

    /**
     * @internal
     */
    public function getData(ResourceDocumentTransformation $transformation, ResourceTransformer $transformer): DataInterface
    {
        $resourceTransformation = new ResourceTransformation(
            $this->getResource(),
            null,
            '',
            $transformation->request,
            $transformation->basePath,
            $transformation->requestedRelationshipName,
            '',
            $transformation->exceptionFactory,
        );
        $data = new CollectionData();

        foreach ($this->getItems() as $item) {
            $resourceTransformation->object = $item;

            $resourceObject = $transformer->transformToResourceObject($resourceTransformation, $data);
            if ($resourceObject !== null) {
                $data->addPrimaryResource($resourceObject);
            }
        }

        return $data;
    }

    public function getRelationshipData(
        ResourceDocumentTransformation $transformation,
        ResourceTransformer $transformer,
        DataInterface $data
    ): ?array {
        return null;
    }

    protected function hasItems(): bool
    {
        return empty($this->getItems()) === false;
    }

    protected function getItems(): iterable
    {
        return $this->object;
    }
}
