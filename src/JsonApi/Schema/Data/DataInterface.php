<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Data;

/**
 * @internal
 */
interface DataInterface
{
    public function getResource(string $type, string $id): ?array;

    public function hasPrimaryResources(): bool;

    public function hasPrimaryResource(string $type, string $id): bool;

    public function hasIncludedResources(): bool;

    public function hasIncludedResource(string $type, string $id): bool;

    public function setPrimaryResources(iterable $transformedResources): static;

    public function addPrimaryResource(array $transformedResource): static;

    public function setIncludedResources(iterable $transformedResources): static;

    public function addIncludedResource(array $transformedResource): static;

    public function transformPrimaryData(): ?iterable;

    public function transformIncluded(): iterable;
}
