<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Exception;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\ClientGeneratedIdAlreadyExists;

class ClientGeneratedIdAlreadyExistsTest extends TestCase
{
    #[Test]
    public function getErrors(): void
    {
        $exception = $this->createException('1');

        $errors = $exception->getErrorDocument()->getErrors();

        self::assertCount(1, $errors);
        self::assertSame('409', $errors[0]->getStatus());
    }

    #[Test]
    public function getClientGeneratedId(): void
    {
        $exception = $this->createException('1');

        $id = $exception->getClientGeneratedId();

        self::assertSame('1', $id);
    }

    private function createException(string $id): ClientGeneratedIdAlreadyExists
    {
        return new ClientGeneratedIdAlreadyExists($id);
    }
}
