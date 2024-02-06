<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Document;

use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Data\SingleResourceData;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceDocumentTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;

use function is_array;

/**
 * @phpstan-type TResource = array{
 *     type: string,
 *     id: string,
 *     attributes: array<string, mixed>,
 *     relationships?: array<string, array{
 *          type: string,
 *          id: string,
 *          attributes: array<string, mixed>
 *     }>,
 *     }
 */
abstract class AbstractSimpleResourceDocument extends AbstractResourceDocument
{
    /**
     * @internal
     */
    public function getData(ResourceDocumentTransformation $transformation, ResourceTransformer $transformer): DataInterface
    {
        $data = new SingleResourceData();

        $data->addPrimaryResource($this->getResource());

        return $data;
    }

    /**
     * @internal
     *
     * @phpstan-return TResource|array{}|null
     */
    public function getRelationshipData(ResourceDocumentTransformation $transformation, ResourceTransformer $transformer, DataInterface $data): ?array
    {
        $relationship = $this->getRelationshipFromResource($this->getResource(), $transformation->requestedRelationshipName);

        if ($relationship === null) {
            $relationship = [];
        } else {
            $data->addPrimaryResource($relationship);
        }

        return $relationship;
    }

    /**
     * The method should return the whole resource including its type, id, attributes, and relationships as an array.
     *
     * @example
     *  return [
     *      "type" => "abc",
     *      "id" => "1",
     *      "attributes" => [
     *          "attribute1" => "value1",
     *      ],
     *  ];
     *
     * @phpstan-return TResource
     */
    abstract protected function getResource(): array;

    /**
     * @phpstan-param TResource $resource
     *
     * @phpstan-return TResource|null
     */
    private function getRelationshipFromResource(array $resource, string $relationshipName): ?array
    {
        if (empty($resource['relationships'][$relationshipName])) {
            return null;
        }

        if (is_array($resource['relationships'][$relationshipName]) === false) {
            return null;
        }

        return $resource['relationships'][$relationshipName];
    }
}
