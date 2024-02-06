<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Resource;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Schema\Resource\ResourceInterface;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubJsonApiRequest;
use WoohooLabs\Yin\Tests\JsonApi\Double\StubResource;

class AbstractResourceTest extends TestCase
{
    #[Test]
    public function initializeTransformation(): void
    {
        $resource = $this->createResource();
        $transformation = $this->createTransformation($resource);

        $resource->initializeTransformation(
            $transformation->request,
            $transformation->object,
            $transformation->exceptionFactory,
        );

        self::assertSame($transformation->request, $resource->getRequest());
        self::assertSame($transformation->object, $resource->getObject());
        self::assertSame($transformation->exceptionFactory, $resource->getExceptionFactory());
    }

    #[Test]
    public function clearTransformation(): void
    {
        $resource = $this->createResource();
        $transformation = $this->createTransformation($resource);

        $resource->initializeTransformation(
            $transformation->request,
            $transformation->object,
            $transformation->exceptionFactory,
        );
        $resource->clearTransformation();

        self::assertNull($resource->getRequest());
        self::assertNull($resource->getObject());
        self::assertNull($resource->getExceptionFactory());
    }

    protected function createResource(): StubResource
    {
        return new StubResource();
    }

    private function createTransformation(ResourceInterface $resource): ResourceTransformation
    {
        return new ResourceTransformation(
            $resource,
            [],
            '',
            new StubJsonApiRequest(),
            '',
            '',
            '',
            new DefaultExceptionFactory(),
        );
    }
}
