<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Hydrator;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipNotExists;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

interface UpdateRelationshipHydratorInterface
{
    /**
     * @throws RelationshipNotExists|JsonApiExceptionInterface
     */
    public function hydrateRelationship(
        string $relationship,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory,
        object $domainObject
    ): mixed;
}
