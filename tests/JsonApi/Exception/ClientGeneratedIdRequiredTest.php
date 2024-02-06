<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ClientGeneratedIdRequired;

class ClientGeneratedIdRequiredTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException();

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('403', $errors[0]->getStatus());
    }

    private function createException(): ClientGeneratedIdRequired
    {
        return new ClientGeneratedIdRequired();
    }
}
