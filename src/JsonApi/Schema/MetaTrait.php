<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema;

trait MetaTrait
{
    protected array $meta = [];

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }
}
