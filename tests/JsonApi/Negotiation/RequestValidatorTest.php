<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Negotiation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnacceptable;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnsupported;
use WoohooLabs\Yin\JsonApi\Exception\QueryParamUnrecognized;
use WoohooLabs\Yin\JsonApi\Exception\RequestBodyInvalidJson;
use WoohooLabs\Yin\JsonApi\Negotiation\RequestValidator;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;

class RequestValidatorTest extends TestCase
{
    /**
     * Test valid request without Request validation Exceptions.
     */
    #[Test]
    public function negotiateWhenValidRequest(): void
    {
        $request = $this->createRequestMock();
        $request->expects(self::once())
            ->method('validateContentTypeHeader');

        $request->expects(self::once())
            ->method('validateAcceptHeader');
        $validator = $this->createRequestValidator();

        $validator->negotiate($request);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('getValidContentTypes')]
    #[Test]
    public function negotiateWhenContentTypeHeaderSupported(string $contentType): void
    {
        // Content-Type and Accept is valid
        $serverRequest = $this->createServerRequest($contentType, 'application/vnd.api+json');
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $validator->negotiate($request);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('getInvalidContentTypes')]
    #[Test]
    public function negotiateWhenContentTypeHeaderUnsupported(string $contentType): void
    {
        // Content-Type is invalid, Accept is valid
        $serverRequest = $this->createServerRequest($contentType, 'application/vnd.api+json');
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $this->expectException(MediaTypeUnsupported::class);

        $validator->negotiate($request);
    }

    #[DataProvider('getValidContentTypes')]
    #[Test]
    public function negotiateWhenAcceptHeaderAcceptable(string $accept): void
    {
        // Content-Type is valid, Accept is invalid
        $serverRequest = $this->createServerRequest('application/vnd.api+json', $accept);
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $validator->negotiate($request);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('getInvalidContentTypes')]
    #[Test]
    public function negotiateWhenAcceptHeaderUnacceptable(string $accept): void
    {
        // Content-Type is valid, Accept is invalid
        $serverRequest = $this->createServerRequest('application/vnd.api+json', $accept);
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $this->expectException(MediaTypeUnacceptable::class);

        $validator->negotiate($request);
    }

    #[Test]
    public function validateQueryParamsWhenValid(): void
    {
        $serverRequest = $this->createServerRequest('application/vnd.api+json');
        $serverRequest->expects(self::once())
            ->method('getQueryParams')
            ->willReturn(
                [
                    'fields' => ['foo' => 'bar'],
                    'include' => 'baz',
                    'sort' => 'asc',
                    'page' => '1',
                    'filter' => 'search',
                    'profile' => 'https://example.com/profiles/last-modified',
                ],
            );

        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $validator->validateQueryParams($request);

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateQueryParamsWhenInvalid(): void
    {
        $serverRequest = $this->createServerRequest('application/vnd.api+json');
        $serverRequest->expects(self::once())
            ->method('getQueryParams')
            ->willReturn(['foo' => 'bar']);
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $this->expectException(QueryParamUnrecognized::class);
        $this->expectExceptionMessage("Query parameter 'foo' can't be recognized!");

        $validator->validateQueryParams($request);
    }

    #[DataProvider('getEmptyMessages')]
    #[Test]
    public function validateJsonBodyWhenEmpty(string $message): void
    {
        $serverRequest = $this->createServerRequest('application/vnd.api+json');
        $this->setFakeBody($serverRequest, $message);
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $validator->validateJsonBody($request);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('getValidJsonMessages')]
    #[Test]
    public function validateJsonBodyWhenValid(string $message): void
    {
        $serverRequest = $this->createServerRequest('application/vnd.api+json');
        $this->setFakeBody($serverRequest, $message);
        $request = $this->createRequest($serverRequest);
        $validator = $this->createRequestValidator();

        $validator->validateJsonBody($request);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('getInvalidJsonMessages')]
    #[Test]
    public function validateJsonBodyWhenInvalid(string $message): void
    {
        $server = $this->createServerRequest('application/vnd.api+json');
        $this->setFakeBody($server, $message);
        $request = $this->createRequest($server);
        $validator = $this->createRequestValidator();

        $this->expectException(RequestBodyInvalidJson::class);

        $validator->validateJsonBody($request);
    }

    public static function getInvalidContentTypes(): array
    {
        return [
            ['application/vnd.api+json; charset=utf-8'],
            ['application/vnd.api+json; ext="ext1,ext2"'],
        ];
    }

    public static function getValidContentTypes(): array
    {
        return [
            ['application/vnd.api+json'],
            ['application/vnd.api+json;profile="https://example.com/profiles/last-modified"'],
            ['application/vnd.api+json;profile="https://example.com/profiles/last-modified", application/vnd.api+json'],
            ['application/vnd.api+json; PROFILE="https://example.com/profiles/last-modified", application/vnd.api+json'],
            ['text/html; charset=utf-8'],
        ];
    }

    public static function getEmptyMessages(): array
    {
        return [
            [''],
        ];
    }

    public static function getValidJsonMessages(): array
    {
        return [
            ['{}'],
            ['{"employees":[
                {"firstName":"John", "lastName":"Doe"},
                {"firstName":"Anna", "lastName":"Smith"},
                {"firstName":"Peter", "lastName":"Jones"}
            ]}',
            ],
        ];
    }

    public static function getInvalidJsonMessages(): array
    {
        return [
            ['{abc'],
            ["{\xEF\xBB\xBF}"],
        ];
    }

    /**
     * @return MockObject|ServerRequestInterface
     */
    private function createServerRequest(string $contentType, string $accept = '')
    {
        $server = $this->getMockForAbstractClass(ServerRequestInterface::class);

        $map = [
            ['content-type', $contentType],
            ['accept', $accept],
        ];
        $server->expects(self::any())
            ->method('getHeaderLine')
            ->willReturnMap($map);

        return $server;
    }

    private function createRequest(ServerRequestInterface $serverRequest): JsonApiRequestInterface
    {
        return new JsonApiRequest($serverRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }

    /**
     * @return MockObject|JsonApiRequestInterface
     */
    private function createRequestWithParsedBody(array $parsedBody): JsonApiRequestInterface
    {
        $request = $this->getMockForAbstractClass(JsonApiRequestInterface::class);

        $request->expects(self::once())
            ->method('getParsedBody')
            ->willReturn($parsedBody);

        return $request;
    }

    private function setFakeBody(ServerRequestInterface $request, string $body): void
    {
        $stream = $this->getMockForAbstractClass(StreamInterface::class);

        $stream->expects(self::once())
            ->method('__toString')
            ->willReturn($body);

        /* @var MockObject $request */
        $request->expects(self::once())
            ->method('getBody')
            ->willReturn($stream);
    }

    /**
     * @return MockObject|JsonApiRequestInterface
     */
    private function createRequestMock()
    {
        /** @var ServerRequestInterface $serverRequest */
        $serverRequest = $this->getMockForAbstractClass(ServerRequestInterface::class);

        /** @var ExceptionFactoryInterface $exceptionFactory */
        $exceptionFactory = $this->getMockForAbstractClass(ExceptionFactoryInterface::class);

        return $this->getMockBuilder(JsonApiRequest::class)
            ->setConstructorArgs([$serverRequest, $exceptionFactory])
            ->getMock();
    }

    private function createRequestValidator(bool $includeOriginalMessageResponse = true): RequestValidator
    {
        return new RequestValidator(new DefaultExceptionFactory(), $includeOriginalMessageResponse);
    }
}
