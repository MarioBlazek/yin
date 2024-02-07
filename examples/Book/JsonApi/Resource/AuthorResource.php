<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Examples\Book\JsonApi\Resource;

use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

class AuthorResource extends AbstractResource
{
    /**
     * @var array
     */
    protected mixed $object;

    /**
     * Provides information about the "type" member of the current resource.
     *
     * The method returns the type of the current resource.
     */
    public function getType(mixed $object): string
    {
        return 'authors';
    }

    /**
     * Provides information about the "id" member of the current resource.
     *
     * The method returns the ID of the current resource which should be a UUID.
     */
    public function getId(mixed $object): string
    {
        return (string) $this->object['id'];
    }

    /**
     * Provides information about the "meta" member of the current resource.
     *
     * The method returns an array of non-standard meta information about the resource. If
     * this array is empty, the member won't appear in the response.
     *
     * @param array $author
     */
    public function getMeta(mixed $object): array
    {
        return [];
    }

    /**
     * Provides information about the "links" member of the current resource.
     *
     * The method returns a new ResourceLinks object if you want to provide linkage
     * data about the resource or null if it should be omitted from the response.
     *
     * @param array $author
     */
    public function getLinks(mixed $author): ?ResourceLinks
    {
        return null;
    }

    /**
     * Provides information about the "attributes" member of the current resource.
     *
     * The method returns an array where the keys signify the attribute names,
     * while the values are callables receiving the domain object as an argument,
     * and they should return the value of the corresponding attribute.
     *
     * @param array $author
     */
    public function getAttributes(mixed $author): array
    {
        return [
            'name' => fn () => $this->object['name'],
        ];
    }

    /**
     * Returns an array of relationship names which are included in the response by default.
     */
    public function getDefaultIncludedRelationships(mixed $author): array
    {
        return [];
    }

    /**
     * Provides information about the "relationships" member of the current resource.
     *
     * The method returns an array where the keys signify the relationship names,
     * while the values are callables receiving the domain object as an argument,
     * and they should return a new relationship instance (to-one or to-many).
     */
    public function getRelationships(mixed $author): array
    {
        return [];
    }
}
