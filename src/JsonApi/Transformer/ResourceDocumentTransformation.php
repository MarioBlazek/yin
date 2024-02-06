<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Transformer;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ResourceDocumentInterface;

/**
 * @internal
 */
final class ResourceDocumentTransformation extends AbstractDocumentTransformation
{
    public string $basePath;
    public string $requestedRelationshipName;

    public function __construct(
        ResourceDocumentInterface $document,
        public $object,
        JsonApiRequestInterface $request,
        string $basePath,
        string $relationpshipName,
        array $additionalMeta,
        ExceptionFactoryInterface $exceptionFactory
    ) {
        parent::__construct($document, $request, $additionalMeta, $exceptionFactory);
        $this->basePath = $basePath;
        $this->requestedRelationshipName = $relationpshipName;
    }
}
