<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Schema;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Schema\MetaTrait;

class MetaTraitTest extends TestCase
{
    #[Test]
    public function getMeta(): void
    {
        $metaTrait = $this->createMetaTrait()
            ->setMeta(['abc' => 'def']);

        $meta = $metaTrait->getMeta();

        self::assertSame(['abc' => 'def'], $meta);
    }

    /**
     * @return mixed
     */
    private function createMetaTrait()
    {
        return $this->getObjectForTrait(MetaTrait::class);
    }
}
