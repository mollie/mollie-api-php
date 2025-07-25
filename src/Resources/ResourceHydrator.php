<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\Http\Response;

class ResourceHydrator
{
    /**
     * Hydrate a response into a resource or collection
     *
     * @param  object|array  $data
     * @return Response|BaseResource|BaseCollection|LazyCollection|IsWrapper
     */
    public function hydrate(BaseResource $resource, $data, Response $response)
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
                    ? $this->parseEmbeddedResources($resource->getConnector(), $resource, $value, $response)
                    : $value;
            }
        }

        $resource->setResponse($response);

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
        Response $response,
        $_links = null
    ): ResourceCollection {
        // Convert object to array for consistent handling
        if (is_object($items)) {
            $items = (array) $items;
        }

        $hydratedItems = array_map(
            fn ($item) => $this->hydrate(
                ResourceFactory::create($response->getConnector(), $collection::getResourceClass()),
                $item,
                $response
            ),
            $items
        );

        if ($_links !== null) {
            $collection->_links = $_links;
        }

        return $collection
            ->setItems($hydratedItems)
            ->setResponse($response);
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
        Response $response
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
                    $response
                )
                : $this->hydrateCollection(
                    ResourceFactory::createCollection($connector, $collectionOrResourceClass),
                    $resourceData,
                    $response
                );
        }

        return $result;
    }
}
