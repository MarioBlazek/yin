<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Exception\InclusionUnsupported;
use WoohooLabs\Yin\JsonApi\Exception\SortingUnsupported;
use WoohooLabs\Yin\JsonApi\JsonApi;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;

class JsonApiTest extends TestCase
{
    #[Test]
    public function getRequest(): void
    {
        $request = $this->createRequest();
        $request = $request->withMethod('PUT');

        $jsonApi = $this->createJsonApi($request);

        self::assertSame($request, $jsonApi->getRequest());
    }

    #[Test]
    public function setRequest(): void
    {
        $request = $this->createRequest()
            ->withMethod('PUT');

        $jsonApi = $this->createJsonApi();
        $jsonApi->setRequest($request);

        self::assertSame($request, $jsonApi->getRequest());
    }

    #[Test]
    public function getResponse(): void
    {
        $response = $this->createResponse()
            ->withStatus(404);

        $jsonApi = $this->createJsonApi(null, $response);

        self::assertSame($response, $jsonApi->getResponse());
    }

    #[Test]
    public function setResponse(): void
    {
        $response = $this->createResponse()
            ->withStatus(404);

        $jsonApi = $this->createJsonApi();
        $jsonApi->setResponse($response);

        self::assertSame($response, $jsonApi->getResponse());
    }

    #[Test]
    public function getPaginationFactory(): void
    {
        $jsonApi = $this->createJsonApi();

        $jsonApi->getPaginationFactory();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function getExceptionFactory(): void
    {
        $exceptionFactory = new DefaultExceptionFactory();

        $jsonApi = $this->createJsonApi(null, null, $exceptionFactory);

        self::assertSame($exceptionFactory, $jsonApi->getExceptionFactory());
    }

    #[Test]
    public function setExceptionFactory(): void
    {
        $exceptionFactory = new DefaultExceptionFactory();

        $jsonApi = $this->createJsonApi();
        $jsonApi->setExceptionFactory($exceptionFactory);

        self::assertSame($exceptionFactory, $jsonApi->getExceptionFactory());
    }

    #[Test]
    public function disableIncludesWhenMissing(): void
    {
        $request = $this->createRequest();

        $this->createJsonApi($request)->disableIncludes();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function disableIncludesWhenEmpty(): void
    {
        $request = $this->createRequest()
            ->withQueryParams(['include' => '']);

        $this->expectException(InclusionUnsupported::class);

        $this->createJsonApi($request)->disableIncludes();
    }

    #[Test]
    public function disableIncludesWhenSet(): void
    {
        $request = $this->createRequest()
            ->withQueryParams(['include' => 'users']);

        $this->expectException(InclusionUnsupported::class);

        $this->createJsonApi($request)->disableIncludes();
    }

    #[Test]
    public function disableSortingWhenMissing(): void
    {
        $request = $this->createRequest();

        $this->createJsonApi($request)->disableSorting();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function disableSortingWhenEmpty(): void
    {
        $request = $this->createRequest()
            ->withQueryParams(['sort' => '']);

        $this->expectException(SortingUnsupported::class);

        $this->createJsonApi($request)->disableSorting();
    }

    #[Test]
    public function disableSortingWhenSet(): void
    {
        $request = $this->createRequest()
            ->withQueryParams(['sort' => 'firstname']);

        $this->expectException(SortingUnsupported::class);

        $this->createJsonApi($request)->disableSorting();
    }

    private function createJsonApi(
        ?JsonApiRequest $request = null,
        ?Response $response = null,
        ?ExceptionFactoryInterface $exceptionFactory = null
    ): JsonApi {
        return new JsonApi(
            $request ?? $this->createRequest(),
            $response ?? new Response(),
            $exceptionFactory ?? new DefaultExceptionFactory(),
        );
    }

    private function createRequest(?ServerRequestInterface $request = null): JsonApiRequest
    {
        return new JsonApiRequest(
            $request ?? new ServerRequest(),
            new DefaultExceptionFactory(),
            new JsonDeserializer(),
        );
    }

    private function createResponse(): Response
    {
        return new Response();
    }
}
