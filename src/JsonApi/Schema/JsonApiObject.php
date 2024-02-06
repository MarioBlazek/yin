<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema;

class JsonApiObject
{
    use MetaTrait;

    public function __construct(private string $version, array $meta = [])
    {
        $this->meta = $meta;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @internal
     */
    public function transform(): array
    {
        $result = [];

        if ($this->version !== '') {
            $result['version'] = $this->version;
        }

        if (empty($this->meta) === false) {
            $result['meta'] = $this->meta;
        }

        return $result;
    }
}
