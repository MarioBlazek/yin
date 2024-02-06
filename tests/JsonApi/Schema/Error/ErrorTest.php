<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Error;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;
use WoohooLabs\Yin\JsonApi\Schema\Link\ErrorLinks;

class ErrorTest extends TestCase
{
    #[Test]
    public function getId(): void
    {
        $error = $this->createError()->setId('123456789');

        $id = $error->getId();

        self::assertSame('123456789', $id);
    }

    public function testGetStatus(): void
    {
        $error = $this->createError()->setStatus('500');

        $status = $error->getStatus();

        self::assertSame('500', $status);
    }

    #[Test]
    public function getCode(): void
    {
        $error = $this->createError()->setCode('UNKNOWN_ERROR');

        $code = $error->getCode();

        self::assertSame('UNKNOWN_ERROR', $code);
    }

    #[Test]
    public function getLinksWhenNull(): void
    {
        $error = $this->createError();

        $links = $error->getLinks();

        self::assertNull($links);
    }

    #[Test]
    public function getLinks(): void
    {
        $links = new ErrorLinks();

        $error = $this->createError()->setLinks($links);

        self::assertSame($links, $error->getLinks());
    }

    #[Test]
    public function getTitle(): void
    {
        $error = $this->createError()->setTitle('Unknown error!');

        $title = $error->getTitle();

        self::assertSame('Unknown error!', $title);
    }

    #[Test]
    public function getDetail(): void
    {
        $error = $this->createError()->setDetail('An unknown error has happened and no solution exists.');

        $detail = $error->getDetail();

        self::assertSame('An unknown error has happened and no solution exists.', $detail);
    }

    #[Test]
    public function getSource(): void
    {
        $source = new ErrorSource('/data/attributes/name', 'name');

        $error = $this->createError()->setSource($source);

        self::assertSame($source, $error->getSource());
    }

    #[Test]
    public function getSourceWhenEmpty(): void
    {
        $error = $this->createError();

        $source = $error->getSource();

        self::assertNull($source);
    }

    #[Test]
    public function transformWithEmptyFields(): void
    {
        $id = '123456789';
        $status = '500';
        $code = 'UNKNOWN_ERROR';
        $title = 'Unknown error!';
        $detail = 'An unknown error has happened and no solution exists.';

        $error = $this->createError()
            ->setId($id)
            ->setStatus($status)
            ->setCode($code)
            ->setTitle($title)
            ->setDetail($detail);

        self::assertSame(
            [
                'id' => $id,
                'status' => $status,
                'code' => $code,
                'title' => $title,
                'detail' => $detail,
            ],
            $error->transform(),
        );
    }

    #[Test]
    public function transform(): void
    {
        $error = $this->createError()
            ->setId('123456789')
            ->setMeta(['abc' => 'def'])
            ->setLinks(new ErrorLinks())
            ->setStatus('500')
            ->setCode('UNKNOWN_ERROR')
            ->setTitle('Unknown error!')
            ->setDetail('An unknown error has happened and no solution exists.')
            ->setSource(new ErrorSource('', ''));

        self::assertSame(
            [
                'id' => '123456789',
                'meta' => ['abc' => 'def'],
                'links' => [],
                'status' => '500',
                'code' => 'UNKNOWN_ERROR',
                'title' => 'Unknown error!',
                'detail' => 'An unknown error has happened and no solution exists.',
                'source' => [],
            ],
            $error->transform(),
        );
    }

    private function createError(): Error
    {
        return new Error();
    }
}
