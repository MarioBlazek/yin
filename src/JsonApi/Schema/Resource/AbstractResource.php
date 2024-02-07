<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Resource;

use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\TransformerTrait;

abstract class AbstractResource implements ResourceInterface
{
    use TransformerTrait;

    protected ?JsonApiRequestInterface $request;
    protected mixed $object;
    protected ?ExceptionFactoryInterface $exceptionFactory;

    /**
     * @internal
     */
    public function initializeTransformation(JsonApiRequestInterface $request, mixed $object, ExceptionFactoryInterface $exceptionFactory): void
    {
        $this->request = $request;
        $this->object = $object;
        $this->exceptionFactory = $exceptionFactory;
    }

    /**
     * @internal
     */
    public function clearTransformation(): void
    {
        $this->request = null;
        $this->object = null;
        $this->exceptionFactory = null;
    }
}
