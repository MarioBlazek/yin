<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Error;

class ErrorSource
{
    public function __construct(private readonly string $pointer, private readonly string $parameter) {}

    public static function fromPointer(string $pointer): self
    {
        return new self($pointer, '');
    }

    public static function fromParameter(string $parameter): self
    {
        return new self('', $parameter);
    }

    public function getPointer(): string
    {
        return $this->pointer;
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    /**
     * @internal
     */
    public function transform(): array
    {
        $content = [];

        if ($this->getPointer() !== '') {
            $content['pointer'] = $this->getPointer();
        }

        if ($this->getParameter() !== '') {
            $content['parameter'] = $this->getParameter();
        }

        return $content;
    }
}
