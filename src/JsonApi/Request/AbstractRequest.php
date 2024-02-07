<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Request;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use WoohooLabs\Yin\JsonApi\Serializer\DeserializerInterface;

abstract class AbstractRequest
{
    protected ServerRequestInterface $serverRequest;
    protected DeserializerInterface $deserializer;
    protected bool $isParsed = false;

    public function __construct(ServerRequestInterface $request, DeserializerInterface $deserializer)
    {
        $this->serverRequest = $request;
        $this->deserializer = $deserializer;
    }

    public function getProtocolVersion(): string
    {
        return $this->serverRequest->getProtocolVersion();
    }

    /**
     * @param string $version HTTP protocol versionWW
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withProtocolVersion($version);

        return $self;
    }

    /**
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->serverRequest->getHeaders();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name case-insensitive header field name
     */
    public function hasHeader(string $name): bool
    {
        return $this->serverRequest->hasHeader($name);
    }

    /**
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        return $this->serverRequest->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->serverRequest->getHeaderLine($name);
    }

    /**
     * @param string $name case-insensitive header field name
     * @param string|string[] $value header value(s)
     */
    public function withHeader(string $name, $value): MessageInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withHeader($name, $value);
        $self->headerChanged($name);

        return $self;
    }

    /**
     * @param string $name case-insensitive header field name to add
     * @param string|string[] $value header value(s)
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withAddedHeader($name, $value);
        $self->headerChanged($name);

        return $self;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withoutHeader($name);
        $self->headerChanged($name);

        return $self;
    }

    public function getBody(): StreamInterface
    {
        return $this->serverRequest->getBody();
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withBody($body);

        return $self;
    }

    public function getRequestTarget(): string
    {
        return $this->serverRequest->getRequestTarget();
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withRequestTarget($requestTarget);

        return $self;
    }

    public function getMethod(): string
    {
        return $this->serverRequest->getMethod();
    }

    public function withMethod(string $method): RequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withMethod($method);

        return $self;
    }

    public function getUri(): UriInterface
    {
        return $this->serverRequest->getUri();
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withUri($uri, $preserveHost);

        return $self;
    }

    public function getServerParams(): array
    {
        return $this->serverRequest->getServerParams();
    }

    public function getCookieParams(): array
    {
        return $this->serverRequest->getCookieParams();
    }

    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withCookieParams($cookies);

        return $self;
    }

    public function getQueryParams(): array
    {
        return $this->serverRequest->getQueryParams();
    }

    public function withQueryParams(array $query): ServerRequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withQueryParams($query);

        foreach ($query as $name => $value) {
            $self->queryParamChanged($name);
        }

        return $self;
    }

    public function getQueryParam(string $name, mixed $default = null): mixed
    {
        $queryParams = $this->serverRequest->getQueryParams();

        return $queryParams[$name] ?? $default;
    }

    public function withQueryParam(string $name, mixed $value): self|static
    {
        $self = clone $this;
        $queryParams = $this->serverRequest->getQueryParams();
        $queryParams[$name] = $value;
        $self->serverRequest = $this->serverRequest->withQueryParams($queryParams);
        $self->queryParamChanged($name);

        return $self;
    }

    public function getUploadedFiles(): array
    {
        return $this->serverRequest->getUploadedFiles();
    }

    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withUploadedFiles($uploadedFiles);

        return $self;
    }

    public function getParsedBody(): null|array|object
    {
        if ($this->isParsed === false) {
            $parsedBody = $this->serverRequest->getParsedBody();
            if ($parsedBody === null || $parsedBody === []) {
                $parsedBody = $this->deserializer->deserialize($this->serverRequest);
                $this->serverRequest = $this->serverRequest->withParsedBody($parsedBody);
                $this->isParsed = true;
            }
        }

        return $this->serverRequest->getParsedBody();
    }

    public function withParsedBody($data): ServerRequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withParsedBody($data);
        $this->isParsed = true;

        return $self;
    }

    public function getAttributes(): array
    {
        return $this->serverRequest->getAttributes();
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->serverRequest->getAttribute($name, $default);
    }

    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withAttribute($name, $value);

        return $self;
    }

    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $self = clone $this;
        $self->serverRequest = $this->serverRequest->withoutAttribute($name);

        return $self;
    }

    abstract protected function headerChanged(string $name): void;

    abstract protected function queryParamChanged(string $name): void;
}
