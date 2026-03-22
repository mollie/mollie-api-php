<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsWrapper;

class ResourceFactory
{
    /**
     * Create a new resource instance.
     */
    public static function create(Connector $connector, string $resourceClass): BaseResource
    {
        /** @var BaseResource $resource */
        $resource = new $resourceClass($connector);

        return $resource;
    }

    /**
     * Create a new collection instance.
     */
    public static function createCollection(
        Connector $connector,
        string $collectionClass,
        array $items = [],
        ?object $_links = null
    ): ResourceCollection {
        /** @var ResourceCollection $collection */
        $collection = new $collectionClass($connector, $items, $_links);

        return $collection;
    }

    /**
     * Create a decorated resource.
     */
    public static function createDecoratedResource($resource, string $decorator): IsWrapper
    {
        if (! is_subclass_of($decorator, IsWrapper::class)) {
            throw new \InvalidArgumentException("The decorator class '{$decorator}' does not implement the ResourceDecorator interface.");
        }

        return $decorator::fromResource($resource);
    }
}
