<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Error;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;

class ErrorSourceTest extends TestCase
{
    #[Test]
    public function createFromPointer(): void
    {
        $pointer = '/data/attributes/name';

        $errorSource = $this->createErrorSource($pointer, '');
        self::assertEquals($errorSource, ErrorSource::fromPointer($pointer));
    }

    #[Test]
    public function createFromParameter(): void
    {
        $parameter = 'name';

        $errorSource = $this->createErrorSource('', $parameter);
        self::assertEquals($errorSource, ErrorSource::fromParameter($parameter));
    }

    #[Test]
    public function getPointer(): void
    {
        $pointer = '/data/attributes/name';

        $errorSource = $this->createErrorSource($pointer, '');
        self::assertSame($pointer, $errorSource->getPointer());
    }

    #[Test]
    public function getParameter(): void
    {
        $parameter = 'name';

        $errorSource = $this->createErrorSource('', $parameter);
        self::assertSame($parameter, $errorSource->getParameter());
    }

    #[Test]
    public function transformWithPointer(): void
    {
        $pointer = '/data/attributes/name';

        $errorSource = $this->createErrorSource($pointer, '');

        $transformedErrorSource = [
            'pointer' => '/data/attributes/name',
        ];
        self::assertSame($transformedErrorSource, $errorSource->transform());
    }

    #[Test]
    public function transformWithParameter(): void
    {
        $parameter = 'name';

        $errorSource = $this->createErrorSource('', $parameter);

        $transformedErrorSource = [
            'parameter' => 'name',
        ];
        self::assertSame($transformedErrorSource, $errorSource->transform());
    }

    #[Test]
    public function transformWithBothAttributes(): void
    {
        $pointer = '/data/attributes/name';
        $parameter = 'name';

        $errorSource = $this->createErrorSource($pointer, $parameter);

        $transformedErrorSource = [
            'pointer' => '/data/attributes/name',
            'parameter' => 'name',
        ];
        self::assertSame($transformedErrorSource, $errorSource->transform());
    }

    private function createErrorSource(string $pointer, string $parameter): ErrorSource
    {
        return new ErrorSource($pointer, $parameter);
    }
}
