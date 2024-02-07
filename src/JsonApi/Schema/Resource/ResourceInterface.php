<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Resource;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;

interface ResourceInterface
{
    /**
     * Provides information about the "type" member of the current resource.
     *
     * The method returns the type of the current resource.
     */
    public function getType(mixed $object): string;

    /**
     * Provides information about the "id" member of the current resource.
     *
     * The method returns the ID of the current resource which should be a UUID.
     */
    public function getId(mixed $object): string;

    /**
     * Provides information about the "meta" member of the current resource.
     *
     * The method returns an array of non-standard meta information about the resource. If
     * this array is empty, the member won't appear in the response.
     */
    public function getMeta(mixed $object): array;

    /**
     * Provides information about the "links" member of the current resource.
     *
     * The method returns a new ResourceLinks object if you want to provide linkage
     * data about the resource or null if it should be omitted from the response.
     */
    public function getLinks(mixed $object): ?ResourceLinks;

    /**
     * Provides information about the "attributes" member of the current resource.
     *
     * The method returns an array where the keys signify the attribute names,
     * while the values are callables receiving the domain object as an argument,
     * and they should return the value of the corresponding attribute.
     *
     * @return callable[]
     */
    public function getAttributes(mixed $object): array;

    /**
     * Returns an array of relationship names which are included in the response by default.
     *
     * @return string[]
     */
    public function getDefaultIncludedRelationships(mixed $object): array;

    /**
     * Provides information about the "relationships" member of the current resource.
     *
     * The method returns an array where the keys signify the relationship names,
     * while the values are callables receiving the domain object as an argument,
     * and they should return a new relationship instance (to-one or to-many).
     *
     * @return callable[]
     */
    public function getRelationships(mixed $object): array;

    /**
     * @internal
     */
    public function initializeTransformation(JsonApiRequestInterface $request, mixed $object, ExceptionFactoryInterface $exceptionFactory): void;

    /**
     * @internal
     */
    public function clearTransformation(): void;
}
