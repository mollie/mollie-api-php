<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\Http\Response;

class ResourceFactory
{
    /**
     * Create resource object from Api result
     */
    public static function createFromApiResult(Connector $connector, $data, string $resourceClass, ?Response $response = null): BaseResource
    {
        if ($data instanceof Response) {
            $response = $data;
            $data = $response->json();
        } elseif ($response === null) {
            throw new \InvalidArgumentException('Response is required');
        }

        /** @var BaseResource $resource */
        $resource = (new $resourceClass($connector))->setResponse($response);

        if ($resource instanceof AnyResource) {
            $resource->fill($data);
        } else {
            foreach ($data as $property => $value) {
                $resource->{$property} = self::holdsEmbeddedResources($resource, $property, $value)
                    ? self::parseEmbeddedResources($connector, $resource, $value, $response)
                    : $value;
            }
        }

        return $resource;
    }

    /**
     * @param  null|array|\ArrayObject  $data
     * @return ResourceCollection
     */
    public static function createResourceCollection(
        Connector $connector,
        string $resourceCollectionClass,
        Response $response,
        $data = null,
        ?object $_links = null
    ): ResourceCollection {
        return self::instantiateResourceCollection(
            $connector,
            $resourceCollectionClass,
            self::mapToResourceObjects($connector, $data ?? [], $resourceCollectionClass::getResourceClass(), $response),
            $response,
            $_links
        );
    }

    /**
     * Create a decorated resource from a response or existing resource.
     *
     * @param  Response|BaseResource|BaseCollection|LazyCollection  $response
     */
    public static function createDecoratedResource($response, string $decorator): IsWrapper
    {
        if (! is_subclass_of($decorator, IsWrapper::class)) {
            throw new \InvalidArgumentException("The decorator class '{$decorator}' does not implement the ResourceDecorator interface.");
        }

        return $decorator::fromResource($response);
    }

    /**
     * Check if the resource holds embedded resources
     *
     * @param  array|\ArrayAccess  $value
     */
    private static function holdsEmbeddedResources(object $resource, string $key, $value): bool
    {
        return $key === '_embedded'
            && ! is_null($value)
            && $resource instanceof EmbeddedResourcesContract;
    }

    /**
     * Parses embedded resources into their respective resource objects or collections.
     */
    private static function parseEmbeddedResources(Connector $connector, object $resource, object $embedded, Response $response): object
    {
        $result = new \stdClass;

        foreach ($embedded as $resourceKey => $resourceData) {
            $collectionOrResourceClass = $resource->getEmbeddedResourcesMap()[$resourceKey] ?? null;

            if (is_null($collectionOrResourceClass)) {
                throw new EmbeddedResourcesNotParseableException(
                    'Resource '.get_class($resource)." does not have a mapping for embedded resource {$resourceKey}"
                );
            }

            $result->{$resourceKey} = is_subclass_of($collectionOrResourceClass, BaseResource::class)
                ? self::createFromApiResult(
                    $connector,
                    $resourceData,
                    $collectionOrResourceClass,
                    $response
                )
                : self::createResourceCollection(
                    $connector,
                    $collectionOrResourceClass,
                    $response,
                    $resourceData
                );
        }

        return $result;
    }

    private static function instantiateResourceCollection(
        Connector $connector,
        string $collectionClass,
        array $items,
        Response $response,
        ?object $_links = null
    ): ResourceCollection {
        return (new $collectionClass($connector, $items, $_links))->setResponse($response);
    }

    /**
     * @param  array|\ArrayObject  $data
     */
    private static function mapToResourceObjects(Connector $connector, $data, string $resourceClass, Response $response): array
    {
        return array_map(
            fn ($item) => static::createFromApiResult(
                $connector,
                $item,
                $resourceClass,
                $response,
            ),
            (array) $data
        );
    }
}
