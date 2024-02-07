<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Data;

use function array_values;

/**
 * @internal
 */
abstract class AbstractData implements DataInterface
{
    protected array $resources = [];
    protected array $primaryKeys = [];
    protected array $includedKeys = [];

    public function getResource(string $type, string $id): ?array
    {
        return $this->resources["{$type}.{$id}"] ?? null;
    }

    public function hasPrimaryResources(): bool
    {
        return empty($this->primaryKeys) === false;
    }

    public function hasPrimaryResource(string $type, string $id): bool
    {
        return isset($this->primaryKeys["{$type}.{$id}"]);
    }

    public function hasIncludedResources(): bool
    {
        return empty($this->includedKeys) === false;
    }

    public function hasIncludedResource(string $type, string $id): bool
    {
        return isset($this->includedKeys["{$type}.{$id}"]);
    }

    public function setPrimaryResources(iterable $transformedResources): static
    {
        $this->primaryKeys = [];
        foreach ($transformedResources as $resource) {
            $this->addPrimaryResource($resource);
        }

        return $this;
    }

    public function addPrimaryResource(array $transformedResource = []): static
    {
        $type = $transformedResource['type'];
        $id = $transformedResource['id'];
        if ($this->hasIncludedResource($type, $id)) {
            unset($this->includedKeys["{$type}.{$id}"]);
        }

        $this->addResourceToPrimaryData($transformedResource);

        return $this;
    }

    public function setIncludedResources(iterable $transformedResources): static
    {
        $this->includedKeys = [];
        foreach ($transformedResources as $resource) {
            $this->addIncludedResource($resource);
        }

        return $this;
    }

    public function addIncludedResource(array $transformedResource): static
    {
        if ($this->hasPrimaryResource($transformedResource['type'], $transformedResource['id']) === false) {
            $this->addResourceToIncludedData($transformedResource);
        }

        return $this;
    }

    public function transformIncluded(): iterable
    {
        return array_values($this->includedKeys);
    }

    protected function addResourceToPrimaryData(array $transformedResource): void
    {
        $type = $transformedResource['type'];
        $id = $transformedResource['id'];
        $key = "{$type}.{$id}";

        $this->resources[$key] = $transformedResource;
        $this->primaryKeys[$key] = &$this->resources[$key];
    }

    protected function addResourceToIncludedData(array $transformedResource): void
    {
        $type = $transformedResource['type'];
        $id = $transformedResource['id'];
        $key = "{$type}.{$id}";

        $this->resources[$key] = $transformedResource;
        $this->includedKeys[$key] = &$this->resources[$key];
    }
}
