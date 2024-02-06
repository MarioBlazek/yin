<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Exception;

use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\Document\ErrorDocumentInterface;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;

use function json_decode;
use function print_r;
use function ucfirst;

class RequestBodyInvalidJsonApi extends AbstractJsonApiException
{
    /**
     * @var JsonApiRequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $validationErrors;

    /**
     * @var bool
     */
    protected $includeOriginalBody;

    public function __construct(JsonApiRequestInterface $request, array $validationErrors, bool $includeOriginalBody)
    {
        parent::__construct('Request body is an invalid JSON:API document!' . print_r($validationErrors, true), 400);
        $this->request = $request;
        $this->validationErrors = $validationErrors;
        $this->includeOriginalBody = $includeOriginalBody;
    }

    public function getErrorDocument(): ErrorDocumentInterface
    {
        $errorDocument = new ErrorDocument($this->getErrors());

        if ($this->includeOriginalBody) {
            $errorDocument->setMeta(['original' => json_decode($this->request->getBody()->__toString(), true)]);
        }

        return $errorDocument;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    protected function getErrors(): array
    {
        $errors = [];
        foreach ($this->validationErrors as $validationError) {
            $error = Error::create()
                ->setStatus('400')
                ->setCode('REQUEST_BODY_INVALID_JSON_API')
                ->setTitle('Request body is an invalid JSON:API document')
                ->setDetail(ucfirst((string) $validationError['message']));

            if (isset($validationError['property']) && $validationError['property'] !== '') {
                $error->setSource(ErrorSource::fromPointer($validationError['property']));
            }

            $errors[] = $error;
        }

        return $errors;
    }
}
