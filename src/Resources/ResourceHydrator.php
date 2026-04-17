<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Contracts\ResourceOrigin;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;

class ResourceHydrator
{
    /**
     * Hydrate raw resource data into a typed resource.
     *
     * @param  object|array  $data
     */
    public function hydrate(BaseResource $resource, $data, ResourceOrigin $origin): BaseResource
    {
        // Convert object to array for consistent handling
        if (is_object($data)) {
            $data = (array) $data;
        }

        if ($resource instanceof AnyResource) {
            $resource->fill($data);
        } else {
            foreach ($data as $property => $value) {
                $resource->{$property} = $this->holdsEmbeddedResources($resource, $property, $value)
                    ? $this->parseEmbeddedResources($resource->getConnector(), $resource, $value, $origin)
                    : $value;
            }
        }

        $resource->setOrigin($origin);

        return $resource;
    }

    /**
     * Hydrate a collection with data.
     *
     * @param  array|object  $items
     * @param  object|null  $_links
     */
    public function hydrateCollection(
        ResourceCollection $collection,
        $items,
        ResourceOrigin $origin,
        $_links = null
    ): ResourceCollection {
        // Convert object to array for consistent handling
        if (is_object($items)) {
            $items = (array) $items;
        }

        $hydratedItems = array_map(
            fn ($item) => $this->hydrate(
                ResourceFactory::create($origin->getConnector(), $collection::getResourceClass()),
                $item,
                $origin
            ),
            $items
        );

        if ($_links !== null) {
            $collection->_links = $_links;
        }

        return $collection
            ->setItems($hydratedItems)
            ->setOrigin($origin);
    }

    private function holdsEmbeddedResources(object $resource, string $key, $value): bool
    {
        return $key === '_embedded'
            && ! is_null($value)
            && $resource instanceof EmbeddedResourcesContract;
    }

    private function parseEmbeddedResources(
        Connector $connector,
        object $resource,
        object $embedded,
        ResourceOrigin $origin
    ): object {
        $result = new \stdClass;

        foreach ($embedded as $resourceKey => $resourceData) {
            $collectionOrResourceClass = $resource->getEmbeddedResourcesMap()[$resourceKey] ?? null;

            if (is_null($collectionOrResourceClass)) {
                throw new EmbeddedResourcesNotParseableException(
                    'Resource '.get_class($resource)." does not have a mapping for embedded resource {$resourceKey}"
                );
            }

            $result->{$resourceKey} = is_subclass_of($collectionOrResourceClass, BaseResource::class)
                ? $this->hydrate(
                    ResourceFactory::create($connector, $collectionOrResourceClass),
                    $resourceData,
                    $origin
                )
                : $this->hydrateCollection(
                    ResourceFactory::createCollection($connector, $collectionOrResourceClass),
                    $resourceData,
                    $origin
                );
        }

        return $result;
    }
}
