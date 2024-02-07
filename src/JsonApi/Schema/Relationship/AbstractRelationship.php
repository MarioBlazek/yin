<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Relationship;

use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Link\RelationshipLinks;
use WoohooLabs\Yin\JsonApi\Schema\MetaTrait;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;

use function call_user_func;

abstract class AbstractRelationship
{
    use MetaTrait;

    protected ?RelationshipLinks $links;
    protected bool $isCallableData = false;
    protected bool $omitDataWhenNotIncluded = false;
    protected ?ResourceInterface $resource;

    final public function __construct(
        array $meta = [],
        ?RelationshipLinks $links = null,
        private mixed $data = null,
        ?ResourceInterface $resource = null
    ) {
        $this->meta = $meta;
        $this->links = $links;
        $this->resource = $resource;
    }

    public static function create(): static
    {
        return new static();
    }

    public static function createWithMeta(array $meta): static
    {
        return new static($meta);
    }

    public static function createWithLinks(?RelationshipLinks $links): static
    {
        return new static([], $links);
    }

    public static function createWithData(array $data, ResourceInterface $resource): static
    {
        return new static([], null, $data, $resource);
    }

    public function getLinks(): ?RelationshipLinks
    {
        return $this->links;
    }

    public function setLinks(RelationshipLinks $links): static
    {
        $this->links = $links;

        return $this;
    }

    public function setData(mixed $data, ResourceInterface $resource): static
    {
        $this->data = $data;
        $this->isCallableData = false;
        $this->resource = $resource;

        return $this;
    }

    public function setDataAsCallable(callable $callableData, ResourceInterface $resource): static
    {
        $this->data = $callableData;
        $this->isCallableData = true;
        $this->resource = $resource;

        return $this;
    }

    public function omitDataWhenNotIncluded(): static
    {
        $this->omitDataWhenNotIncluded = true;

        return $this;
    }

    /**
     * @internal
     */
    public function transform(
        ResourceTransformation $transformation,
        ResourceTransformer $resourceTransformer,
        DataInterface $data,
        array $defaultRelationships
    ): ?array {
        $requestedRelationshipName = $transformation->requestedRelationshipName;
        $currentRelationshipName = $transformation->currentRelationshipName;
        $basePath = $transformation->basePath;

        $isCurrentRelationship = $requestedRelationshipName !== '' && $currentRelationshipName === $requestedRelationshipName;
        $isIncludedField = $transformation->request->isIncludedField($transformation->resourceType, $currentRelationshipName);
        $isIncludedRelationship = $transformation->request->isIncludedRelationship($basePath, $currentRelationshipName, $defaultRelationships);

        // The relationship is not needed at all
        if ($isCurrentRelationship === false && $isIncludedField === false && $isIncludedRelationship === false) {
            return null;
        }

        // Transform the relationship data
        $dataMember = false;
        if (
            ($isCurrentRelationship === true || $isIncludedRelationship === true || $this->omitDataWhenNotIncluded === false)
            && ($isCurrentRelationship === true || $requestedRelationshipName === '')
        ) {
            $dataMember = $this->transformData($transformation, $resourceTransformer, $data, $defaultRelationships);
        }

        // The relationship field is not included
        if ($isIncludedField === false) {
            return null;
        }

        // Transform the relationship link because the relationship field is included
        $relationshipObject = [];

        if ($this->links !== null) {
            $relationshipObject['links'] = $this->links->transform();
        }

        if (empty($this->meta) === false) {
            $relationshipObject['meta'] = $this->meta;
        }

        if ($dataMember !== false) {
            $relationshipObject['data'] = $dataMember;
        }

        return $relationshipObject;
    }

    /**
     * @internal
     */
    abstract protected function transformData(
        ResourceTransformation $transformation,
        ResourceTransformer $resourceTransformer,
        DataInterface $data,
        array $defaultRelationships
    ): false|array|null;

    /**
     * @internal
     */
    protected function getData(): mixed
    {
        return $this->isCallableData ? call_user_func($this->data, $this) : $this->data;
    }

    /**
     * @internal
     */
    protected function transformResourceIdentifier(
        ResourceTransformation $transformation,
        ResourceTransformer $resourceTransformer,
        DataInterface $data,
        mixed $object,
        array $defaultRelationships
    ): ?array {
        $relationshipTransformation = clone $transformation;
        $relationshipTransformation->resourceType = '';
        $relationshipTransformation->resource = $this->resource;
        $relationshipTransformation->object = $object;

        $basePath = $transformation->basePath;
        $basePath .= ($basePath !== '' ? '.' : '') . $relationshipTransformation->currentRelationshipName;
        $relationshipTransformation->basePath = $basePath;

        if (
            $transformation->request->isIncludedRelationship(
                $transformation->basePath,
                $transformation->currentRelationshipName,
                $defaultRelationships,
            )
        ) {
            $resource = $resourceTransformer->transformToResourceObject($relationshipTransformation, $data);
            if ($resource !== null) {
                $data->addIncludedResource($resource);
            }
        }

        return $resourceTransformer->transformToResourceIdentifier($relationshipTransformation);
    }
}
