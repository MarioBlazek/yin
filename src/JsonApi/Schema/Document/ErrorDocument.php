<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Document;

use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

class ErrorDocument extends AbstractErrorDocument
{
    protected ?JsonApiObject $jsonApi = null;
    protected array $meta = [];
    protected ?DocumentLinks $links = null;

    /**
     * @param Error[] $errors
     */
    public function __construct(array $errors = [])
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    /**
     * @param Error[] $errors
     */
    public static function create(array $errors = []): self
    {
        return new self($errors);
    }

    public function getJsonApi(): ?JsonApiObject
    {
        return $this->jsonApi;
    }

    public function setJsonApi(?JsonApiObject $jsonApi): self
    {
        $this->jsonApi = $jsonApi;

        return $this;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function getLinks(): ?DocumentLinks
    {
        return $this->links;
    }

    public function setLinks(?DocumentLinks $links): self
    {
        $this->links = $links;

        return $this;
    }
}
