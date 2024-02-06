<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Request\AbstractRequest;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;

class AbstractRequestTest extends TestCase
{
    #[Test]
    public function getProtocolVersion(): void
    {
        $protocolVersion = '2';

        $request = $this->createRequest()->withProtocolVersion($protocolVersion);
        self::assertSame($protocolVersion, $request->getProtocolVersion());
    }

    #[Test]
    public function getHeaders(): void
    {
        $header1Name = 'a';
        $header1Value = 'b';
        $header2Name = 'c';
        $header2Value = 'd';
        $headers = [$header1Name => [$header1Value], $header2Name => [$header2Value]];

        $request = $this->createRequestWithHeader($header1Name, $header1Value)->withHeader($header2Name, $header2Value);
        self::assertSame($headers, $request->getHeaders());
    }

    #[Test]
    public function hasHeaderWhenHeaderNotExists(): void
    {
        $request = $this->createRequestWithHeader('a', 'b');

        self::assertFalse($request->hasHeader('c'));
    }

    #[Test]
    public function hasHeaderWhenHeaderExists(): void
    {
        $request = $this->createRequestWithHeader('a', 'b');

        self::assertTrue($request->hasHeader('a'));
    }

    #[Test]
    public function getHeaderWhenHeaderExists(): void
    {
        $request = $this->createRequestWithHeader('a', 'b');

        self::assertSame(['b'], $request->getHeader('a'));
    }

    #[Test]
    public function getHeaderLineWhenHeaderNotExists(): void
    {
        $request = $this->createRequestWithHeaders(['a' => ['b', 'c', 'd']]);

        self::assertSame('', $request->getHeaderLine('b'));
    }

    #[Test]
    public function getHeaderLineWhenHeaderExists(): void
    {
        $request = $this->createRequestWithHeaders(['a' => ['b', 'c', 'd']]);

        self::assertSame('b,c,d', $request->getHeaderLine('a'));
    }

    #[Test]
    public function withHeader(): void
    {
        $headers = [];
        $headerName = 'a';
        $headerValue = 'b';

        $request = $this->createRequestWithHeaders($headers);
        $newRequest = $request->withHeader($headerName, $headerValue);
        self::assertSame([], $request->getHeader($headerName));
        self::assertSame([$headerValue], $newRequest->getHeader($headerName));
    }

    #[Test]
    public function withAddedHeader(): void
    {
        $headerName = 'a';
        $headerValue = 'b';
        $headers = [$headerName => $headerValue];

        $request = $this->createRequestWithHeaders($headers);
        $newRequest = $request->withAddedHeader($headerName, $headerValue);
        self::assertSame([$headerValue], $request->getHeader($headerName));
        self::assertSame([$headerValue, $headerValue], $newRequest->getHeader($headerName));
    }

    #[Test]
    public function withoutHeader(): void
    {
        $headerName = 'a';
        $headerValue = 'b';
        $headers = [$headerName => $headerValue];

        $request = $this->createRequestWithHeaders($headers);
        $newRequest = $request->withoutHeader($headerName);

        self::assertSame([$headerValue], $request->getHeader($headerName));
        self::assertSame([], $newRequest->getHeader($headerName));
    }

    #[Test]
    public function getBody(): void
    {
        $body = new Stream('php://input');

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::once())
            ->method('getBody')
            ->willReturn($body);

        $request = $this->createRequest($serverRequest);

        self::assertSame($body, $request->getBody());
    }

    #[Test]
    public function withBody(): void
    {
        $body = new Stream('php://input');

        $request = $this->createRequest();
        $request = $request->withBody($body);

        self::assertSame($body, $request->getBody());
    }

    #[Test]
    public function getRequestTarget(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::once())
            ->method('getRequestTarget')
            ->willReturn('/abc');

        $request = $this->createRequest($serverRequest);

        self::assertSame('/abc', $request->getRequestTarget());
    }

    #[Test]
    public function withRequestTarget(): void
    {
        $request = $this->createRequest();

        $request = $request->withRequestTarget('/abc');

        self::assertSame('/abc', $request->getRequestTarget());
    }

    #[Test]
    public function getMethod(): void
    {
        $method = 'PUT';

        $request = $this->createRequest();
        $newRequest = $request->withMethod($method);
        self::assertSame('GET', $request->getMethod());
        self::assertSame($method, $newRequest->getMethod());
    }

    #[Test]
    public function getUri(): void
    {
        $uri = new Uri();

        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $request = $this->createRequest($serverRequest);

        self::assertSame($uri, $request->getUri());
    }

    #[Test]
    public function withUri(): void
    {
        $request = $this->createRequest();

        $request = $request->withUri(new Uri('https://example.com'));

        self::assertSame('https://example.com', $request->getUri()->__toString());
    }

    #[Test]
    public function getServerParams(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::once())
            ->method('getServerParams')
            ->willReturn(['abc' => 'def']);

        $request = $this->createRequest($serverRequest);

        self::assertSame(['abc' => 'def'], $request->getServerParams());
    }

    #[Test]
    public function getCookieParams(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::once())
            ->method('getCookieParams')
            ->willReturn(['abc' => 'def']);

        $request = $this->createRequest($serverRequest);

        self::assertSame(['abc' => 'def'], $request->getCookieParams());
    }

    #[Test]
    public function withCookieParams(): void
    {
        $request = $this->createRequest();

        $request = $request->withCookieParams(['abc' => 'def']);

        self::assertSame(['abc' => 'def'], $request->getCookieParams());
    }

    #[Test]
    public function getUploadedFiles(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::once())
            ->method('getUploadedFiles')
            ->willReturn(['abc']);

        $request = $this->createRequest($serverRequest);

        self::assertSame(['abc'], $request->getUploadedFiles());
    }

    #[Test]
    public function getQueryParams(): void
    {
        $queryParamName = 'abc';
        $queryParamValue = 'cde';
        $queryParams = [$queryParamName => $queryParamValue];

        $request = $this->createRequest();
        $newRequest = $request->withQueryParams($queryParams);
        self::assertSame([], $request->getQueryParams());
        self::assertSame($queryParams, $newRequest->getQueryParams());
    }

    #[Test]
    public function getQueryParamWhenNotFound(): void
    {
        $queryParams = [];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame('xyz', $request->getQueryParam('a_b', 'xyz'));
    }

    #[Test]
    public function getQueryParamWhenNotEmpty(): void
    {
        $queryParamName = 'abc';
        $queryParamValue = 'cde';
        $queryParams = [$queryParamName => $queryParamValue];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($queryParamValue, $request->getQueryParam($queryParamName));
    }

    #[Test]
    public function withQueryParam(): void
    {
        $queryParams = [];
        $addedQueryParamName = 'abc';
        $addedQueryParamValue = 'def';

        $request = $this->createRequestWithQueryParams($queryParams);
        $newRequest = $request->withQueryParam($addedQueryParamName, $addedQueryParamValue);
        self::assertNull($request->getQueryParam($addedQueryParamName));
        self::assertSame($addedQueryParamValue, $newRequest->getQueryParam($addedQueryParamName));
    }

    #[Test]
    public function getParsedBody(): void
    {
        $parsedBody = [
            'data' => [
                'type' => 'cat',
                'id' => 'tom',
            ],
        ];

        $request = $this->createRequest();
        $newRequest = $request->withParsedBody($parsedBody);
        self::assertNull($request->getParsedBody());
        self::assertSame($parsedBody, $newRequest->getParsedBody());
    }

    #[Test]
    public function getAttributes(): void
    {
        $attribute1Key = 'a';
        $attribute1Value = true;
        $attribute2Key = 'b';
        $attribute2Value = 123456;
        $attributes = [$attribute1Key => $attribute1Value, $attribute2Key => $attribute2Value];

        $request = $this->createRequest();
        $newRequest = $request
            ->withAttribute($attribute1Key, $attribute1Value)
            ->withAttribute($attribute2Key, $attribute2Value);

        self::assertSame([], $request->getAttributes());
        self::assertSame($attributes, $newRequest->getAttributes());
        self::assertSame($attribute1Value, $newRequest->getAttribute($attribute1Key));
    }

    #[Test]
    public function withoutAttributes(): void
    {
        $request = $this->createRequest();
        $newRequest = $request
            ->withAttribute('abc', 'cde')
            ->withoutAttribute('abc');

        self::assertSame([], $request->getAttributes());
        self::assertEmpty($newRequest->getAttributes());
    }

    private function createRequest(?ServerRequestInterface $serverRequest = null): JsonApiRequest
    {
        return new JsonApiRequest(
            $serverRequest ?? new ServerRequest(),
            new DefaultExceptionFactory(),
            new JsonDeserializer(),
        );
    }

    private function createRequestWithHeaders(array $headers): AbstractRequest
    {
        $psrRequest = new ServerRequest([], [], null, null, 'php://temp', $headers);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }

    private function createRequestWithHeader(string $headerName, string $headerValue): AbstractRequest
    {
        $psrRequest = new ServerRequest([], [], null, null, 'php://temp', [$headerName => $headerValue]);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }

    private function createRequestWithQueryParams(array $queryParams): AbstractRequest
    {
        $psrRequest = new ServerRequest();
        $psrRequest = $psrRequest->withQueryParams($queryParams);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }
}
