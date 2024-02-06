<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnsupported;

class MediaTypeUnsupportedTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('415', $errors[0]->getStatus());
    }

    #[Test]
    public function getMediaTypeName(): void
    {
        $exception = $this->createException('media-type');

        $mediaTypeName = $exception->getMediaTypeName();

        self::assertSame('media-type', $mediaTypeName);
    }

    private function createException(string $mediaType): MediaTypeUnsupported
    {
        return new MediaTypeUnsupported($mediaType);
    }
}
