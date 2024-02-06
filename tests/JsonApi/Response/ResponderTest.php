<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Response;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Response\Responder;
use WoohooLabs\Yin\JsonApi\Schema\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Serializer\JsonSerializer;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResourceDocument;

use function json_decode;

class ResponderTest extends TestCase
{
    #[Test]
    public function ok(): void
    {
        $response = $this->createResponder()->ok(new StubResourceDocument(), []);

        $statusCode = $response->getStatusCode();

        self::assertSame(200, $statusCode);
    }

    #[Test]
    public function okWithoutLinks(): void
    {
        $response = $this->createResponder()->ok(
            new StubResourceDocument(),
            [],
        );

        $contentType = $response->getHeaderLine('content-type');

        self::assertSame('application/vnd.api+json', $contentType);
    }

    #[Test]
    public function okWithLinksWithoutProfiles(): void
    {
        $response = $this->createResponder()->ok(
            new StubResourceDocument(
                null,
                [],
                DocumentLinks::createWithoutBaseUri(),
            ),
            [],
        );

        $contentType = $response->getHeaderLine('content-type');

        self::assertSame('application/vnd.api+json', $contentType);
    }

    #[Test]
    public function nokWithProfiles(): void
    {
        $response = $this->createResponder()->ok(
            new StubResourceDocument(
                null,
                [],
                DocumentLinks::createWithoutBaseUri()
                    ->addProfile(new Link('https://example.com/profiles/last-modified'))
                    ->addProfile(new Link('https://example.com/profiles/created')),
            ),
            [],
        );

        $contentType = $response->getHeaderLine('content-type');

        self::assertSame(
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
            $contentType,
        );
    }

    #[Test]
    public function okWithMeta(): void
    {
        $response = $this->createResponder()->okWithMeta(new StubResourceDocument(null, ['abc' => 'def']), []);

        $statusCode = $response->getStatusCode();
        $meta = json_decode($response->getBody()->__toString(), true)['meta'];

        self::assertSame(200, $statusCode);
        self::assertSame('def', $meta['abc']);
    }

    #[Test]
    public function okWithRelationship(): void
    {
        $response = $this->createResponder()->okWithRelationship('', new StubResourceDocument(), []);

        $statusCode = $response->getStatusCode();

        self::assertSame(200, $statusCode);
    }

    #[Test]
    public function created(): void
    {
        $response = $this->createResponder()->created(new StubResourceDocument(), []);

        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->__toString(), true);

        self::assertSame(201, $statusCode);
        self::assertNotEmpty($body);
    }

    #[Test]
    public function createdWithLinks(): void
    {
        $response = $this->createResponder()->created(
            new StubResourceDocument(
                null,
                [],
                new DocumentLinks('', ['self' => new Link('https://example.com/users')]),
            ),
            [],
        );

        $location = $response->getHeader('location');

        self::assertSame(['https://example.com/users'], $location);
    }

    #[Test]
    public function createdWithMeta(): void
    {
        $response = $this->createResponder()->createdWithMeta(
            new StubResourceDocument(
                null,
                [],
                new DocumentLinks('', ['self' => new Link('https://example.com/users')]),
            ),
            [],
        );

        $statusCode = $response->getStatusCode();
        $location = $response->getHeader('location');
        $body = json_decode($response->getBody()->__toString(), true);

        self::assertSame(201, $statusCode);
        self::assertSame(['https://example.com/users'], $location);
        self::assertNotEmpty($body);
    }

    #[Test]
    public function createdWithRelationship(): void
    {
        $response = $this->createResponder()->createdWithRelationship('', new StubResourceDocument(), []);

        $statusCode = $response->getStatusCode();

        self::assertSame(201, $statusCode);
    }

    #[Test]
    public function accepted(): void
    {
        $response = $this->createResponder()->accepted();

        $statusCode = $response->getStatusCode();

        self::assertSame(202, $statusCode);
    }

    #[Test]
    public function noContent(): void
    {
        $response = $this->createResponder()->noContent();

        $statusCode = $response->getStatusCode();

        self::assertSame(204, $statusCode);
    }

    #[Test]
    public function forbidden(): void
    {
        $response = $this->createResponder()->forbidden(new ErrorDocument());

        $statusCode = $response->getStatusCode();

        self::assertSame(403, $statusCode);
    }

    #[Test]
    public function notFound(): void
    {
        $response = $this->createResponder()->notFound(new ErrorDocument());

        $statusCode = $response->getStatusCode();

        self::assertSame(404, $statusCode);
    }

    #[Test]
    public function notFoundWithProfiles(): void
    {
        $response = $this->createResponder()->notFound(
            ErrorDocument::create()
                ->setLinks(
                    DocumentLinks::createWithoutBaseUri()
                        ->addProfile(new Link('https://example.com/profiles/last-modified'))
                        ->addProfile(new Link('https://example.com/profiles/created')),
                ),
        );

        $contentType = $response->getHeaderLine('content-type');

        self::assertSame(
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
            $contentType,
        );
    }

    #[Test]
    public function conflict(): void
    {
        $response = $this->createResponder()->conflict(new ErrorDocument());

        $statusCode = $response->getStatusCode();

        self::assertSame(409, $statusCode);
    }

    #[Test]
    public function genericSuccess(): void
    {
        $response = $this->createResponder()->genericSuccess(201);

        $statusCode = $response->getStatusCode();

        self::assertSame(201, $statusCode);
    }

    #[Test]
    public function genericError(): void
    {
        $response = $this->createResponder()->genericError(
            new ErrorDocument([new Error(), new Error()]),
            418,
        );

        $statusCode = $response->getStatusCode();
        $errors = json_decode($response->getBody()->__toString(), true)['errors'];

        self::assertSame(418, $statusCode);
        self::assertCount(2, $errors);
    }

    private function createResponder(): Responder
    {
        return Responder::create(
            new StubJsonApiRequest(),
            new Response(),
            new DefaultExceptionFactory(),
            new JsonSerializer(),
        );
    }
}
