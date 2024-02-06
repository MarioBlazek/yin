<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Double;

use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\AbstractRelationship;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformation;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformer;

class FakeRelationship extends AbstractRelationship
{
    /**
     * @return mixed
     */
    public function getRelationshipData()
    {
        return $this->getData();
    }

    public function isOmitDataWhenNotIncluded(): bool
    {
        return $this->omitDataWhenNotIncluded;
    }

    protected function transformData(
        ResourceTransformation $transformation,
        ResourceTransformer $resourceTransformer,
        DataInterface $data,
        array $defaultRelationships
    ): ?array {
        return [];
    }
}
