<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Response;

use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ErrorDocumentInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ResourceDocumentInterface;
use WoohooLabs\Yin\JsonApi\Serializer\SerializerInterface;
use WoohooLabs\Yin\JsonApi\Transformer\DocumentTransformer;

class Responder extends AbstractResponder
{
    public function __construct(
        JsonApiRequestInterface $request,
        ResponseInterface $response,
        ExceptionFactoryInterface $exceptionFactory,
        SerializerInterface $serializer
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->documentTransformer = new DocumentTransformer();
        $this->exceptionFactory = $exceptionFactory;
        $this->serializer = $serializer;
    }

    public static function create(
        JsonApiRequestInterface $request,
        ResponseInterface $response,
        ExceptionFactoryInterface $exceptionFactory,
        SerializerInterface $serializer
    ): self {
        return new self($request, $response, $exceptionFactory, $serializer);
    }

    /**
     * Returns a "200 Ok" response, containing a document in the body with the resource.
     */
    public function ok(ResourceDocumentInterface $document, mixed $object, array $additionalMeta = []): ResponseInterface
    {
        return $this->getResourceResponse($document, $object, 200, $additionalMeta);
    }

    /**
     * Returns a "200 Ok" response, containing a document in the body with the resource metadata.
     */
    public function okWithMeta(ResourceDocumentInterface $document, mixed $object, array $additionalMeta = []): ResponseInterface
    {
        return $this->getMetaResponse($document, $object, 200, $additionalMeta);
    }

    /**
     * Returns a "200 Ok" response, containing a document in the body with the relationship. You can also
     * pass additional meta information for the document in the $additionalMeta argument.
     */
    public function okWithRelationship(
        string $relationshipName,
        ResourceDocumentInterface $document,
        mixed $object,
        array $additionalMeta = []
    ): ResponseInterface {
        return $this->getRelationshipResponse(
            $relationshipName,
            $document,
            $object,
            200,
            $additionalMeta,
        );
    }

    /**
     * Returns a "201 Created" response, containing a document in the body with the newly created resource. You can also
     * pass additional meta information for the document in the $additionalMeta argument.
     */
    public function created(ResourceDocumentInterface $document, mixed $object, array $additionalMeta = []): ResponseInterface
    {
        $response = $this->getResourceResponse($document, $object, 201, $additionalMeta);

        return $this->getResponseWithLocationHeader($document, $response);
    }

    /**
     * Returns a "201 Created" response, containing a document in the body with the newly created resource metadata.
     * You can also pass additional meta information for the document in the $additionalMeta argument.
     */
    public function createdWithMeta(ResourceDocumentInterface $document, mixed $object, array $additionalMeta = []): ResponseInterface
    {
        $response = $this->getMetaResponse($document, $object, 201, $additionalMeta);

        return $this->getResponseWithLocationHeader($document, $response);
    }

    /**
     * Returns a "200 Ok" response, containing a document in the body with the relationship. You can also
     * pass additional meta information for the document in the $additionalMeta argument.
     */
    public function createdWithRelationship(
        string $relationshipName,
        ResourceDocumentInterface $document,
        mixed $object,
        array $additionalMeta = []
    ): ResponseInterface {
        return $this->getRelationshipResponse(
            $relationshipName,
            $document,
            $object,
            201,
            $additionalMeta,
        );
    }

    /**
     * Returns a "202 Accepted" response.
     */
    public function accepted(): ResponseInterface
    {
        return $this->response->withStatus(202);
    }

    /**
     * Returns a "204 No Content" response.
     */
    public function noContent(): ResponseInterface
    {
        return $this->response->withStatus(204);
    }

    /**
     * Returns a "403 Forbidden" response, containing a document in the body with the errors. You can also pass
     * additional meta information for the error document in the $additionalMeta argument.
     */
    public function forbidden(ErrorDocumentInterface $document, array $additionalMeta = []): ResponseInterface
    {
        return $this->getErrorResponse($document, 403, $additionalMeta);
    }

    /**
     * Returns a "404 Not Found" response, containing a document in the body with the errors. You can also pass
     * additional meta information for the error document in the $additionalMeta argument.
     */
    public function notFound(ErrorDocumentInterface $document, array $additionalMeta = []): ResponseInterface
    {
        return $this->getErrorResponse($document, 404, $additionalMeta);
    }

    /**
     * Returns a "409 Conflict" response, containing a document in the body with the errors. You can also pass
     * additional meta information for the error document in the $additionalMeta argument.
     */
    public function conflict(ErrorDocumentInterface $document, array $additionalMeta = []): ResponseInterface
    {
        return $this->getErrorResponse($document, 409, $additionalMeta);
    }

    /**
     * Returns a successful response with the given status code.
     */
    public function genericSuccess(int $statusCode): ResponseInterface
    {
        return $this->response->withStatus($statusCode);
    }

    /**
     * Returns an error response, containing a document in the body with the errors. You can also pass additional
     * meta information to the document in the $additionalMeta argument.
     */
    public function genericError(ErrorDocumentInterface $document, ?int $statusCode = null, array $additionalMeta = []): ResponseInterface
    {
        return $this->getErrorResponse($document, $statusCode, $additionalMeta);
    }
}
