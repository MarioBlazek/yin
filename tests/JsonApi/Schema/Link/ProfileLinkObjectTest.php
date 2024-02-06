<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema\Link;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\Link\ProfileLinkObject;

class ProfileLinkObjectTest extends TestCase
{
    #[Test]
    public function getAliases(): void
    {
        $link = $this->createProfileLinkObject(['keyword' => 'alias']);

        self::assertSame(['keyword' => 'alias'], $link->getAliases());
    }

    #[Test]
    public function getAliasWhenPresent(): void
    {
        $link = $this->createProfileLinkObject(['keyword' => 'alias']);

        self::assertSame('alias', $link->getAlias('keyword'));
    }

    #[Test]
    public function getAliasWhenNotPresent(): void
    {
        $link = $this->createProfileLinkObject(['keyword' => 'alias']);

        self::assertSame('', $link->getAlias('key'));
    }

    #[Test]
    public function addAliases(): void
    {
        $link = $this->createProfileLinkObject();

        $link->addAlias('keyword1', 'alias1');
        $link->addAlias('keyword2', 'alias2');

        self::assertSame(['keyword1' => 'alias1', 'keyword2' => 'alias2'], $link->getAliases());
    }

    #[Test]
    public function transformLinkWithAlias(): void
    {
        $link = $this->createProfileLinkObject(['keyword' => 'alias']);

        $transformedLink = $link->transform('');

        self::assertArrayHasKey('aliases', $transformedLink);
        self::assertSame(['keyword' => 'alias'], $transformedLink['aliases']);
    }

    #[Test]
    public function transformLinkWithoutAlias(): void
    {
        $link = $this->createProfileLinkObject([]);

        $transformedLink = $link->transform('');

        self::assertArrayNotHasKey('aliases', $transformedLink);
    }

    private function createProfileLinkObject(array $aliases = []): ProfileLinkObject
    {
        return new ProfileLinkObject('', [], $aliases);
    }
}
