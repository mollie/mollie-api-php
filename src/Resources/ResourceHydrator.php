<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Contracts\ResourceOrigin;

class ResourceHydrator
{
    public function __construct(private ?ResourcePropertyTypeResolver $propertyTypes = null)
    {
    }

    /**
     * Hydrate raw resource data into a typed resource.
     *
     * @param  BaseResource  $resource
     * @param  object|array  $data
     * @param  ResourceOrigin  $origin
     */
    public function hydrate(BaseResource $resource, $data, ResourceOrigin $origin): BaseResource
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        if ($resource instanceof AnyResource) {
            $resource->fill($data);
        } else {
            $typeMap = $this->propertyTypes()->typesFor($resource);

            foreach ($data as $property => $value) {
                $property = (string) $property;

                if ($this->holdsEmbeddedResources($resource, $property, $value)) {
                    $resource->{$property} = $this->parseEmbeddedResources(
                        $resource->getConnector(),
                        $resource,
                        $value,
                        $origin
                    );

                    continue;
                }

                if (isset($typeMap[$property])) {
                    $resource->{$property} = $this->propertyTypes()->cast($typeMap[$property], $value);

                    continue;
                }

                $resource->{$property} = $value;
            }
        }

        $resource->setOrigin($origin);

        return $resource;
    }

    /**
     * Hydrate a collection with data.
     *
     * @param  ResourceCollection  $collection
     * @param  array|object  $items
     * @param  ResourceOrigin  $origin
     * @param  object|null  $_links
     */
    public function hydrateCollection(
        ResourceCollection $collection,
        $items,
        ResourceOrigin $origin,
        $_links = null
    ): ResourceCollection {
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

    private function propertyTypes(): ResourcePropertyTypeResolver
    {
        return $this->propertyTypes ??= new ResourcePropertyTypeResolver;
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
                $result->{$resourceKey} = $this->hydrate(
                    ResourceFactory::create($connector, AnyResource::class),
                    $resourceData,
                    $origin
                );

                continue;
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
