<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Data;

use function array_key_first;

/**
 * @internal
 */
final class SingleResourceData extends AbstractData
{
    public function transformPrimaryData(): ?iterable
    {
        if ($this->hasPrimaryResources() === false) {
            return null;
        }
        $key = array_key_first($this->primaryKeys);

        return $this->resources[$key];
    }
}
