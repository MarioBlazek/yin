<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnacceptable;

class MediaTypeUnacceptableTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('406', $errors[0]->getStatus());
    }

    #[Test]
    public function getMediaTypeName(): void
    {
        $exception = $this->createException('media-type');

        $mediaType = $exception->getMediaTypeName();

        self::assertSame('media-type', $mediaType);
    }

    private function createException(string $mediaType): MediaTypeUnacceptable
    {
        return new MediaTypeUnacceptable($mediaType);
    }
}
