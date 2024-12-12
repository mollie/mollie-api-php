<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\Http\Response;

#[\AllowDynamicProperties]
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
        }

        /** @var BaseResource $resource */
        $resource = new $resourceClass($connector, $response);

        if ($resource instanceof AnyResource) {
            $resource->fill($data);
        } else {
            foreach ($data as $property => $value) {
                $resource->{$property} = self::holdsEmbeddedResources($resource, $property, $value)
                ? self::parseEmbeddedResources($connector, $resource, $value)
                : $value;
            }
        }

        return $resource;
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
    private static function parseEmbeddedResources(Connector $connector, object $resource, object $embedded): object
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
                    $collectionOrResourceClass
                )
                : self::createEmbeddedResourceCollection(
                    $connector,
                    $collectionOrResourceClass,
                    $resourceData
                );
        }

        return $result;
    }

    /**
     * @param  array|\ArrayObject  $data
     */
    private static function createEmbeddedResourceCollection(
        Connector $connector,
        string $collectionClass,
        $data
    ): BaseCollection {
        return self::instantiateBaseCollection(
            $connector,
            $collectionClass,
            self::mapToResourceObjects(
                $connector,
                $data,
                $collectionClass::getResourceClass()
            ),
        );
    }

    /**
     * @param  null|array|\ArrayObject  $data
     */
    public static function createBaseResourceCollection(
        Connector $connector,
        string $resourceClass,
        $data = null,
        ?object $_links = null,
        ?string $resourceCollectionClass = null,
        ?Response $response = null
    ): BaseCollection {
        return self::instantiateBaseCollection(
            $connector,
            self::determineCollectionClass($resourceClass, $resourceCollectionClass),
            self::mapToResourceObjects($connector, $data ?? [], $resourceClass, $response),
            $_links,
            $response
        );
    }

    private static function instantiateBaseCollection(
        Connector $connector,
        string $collectionClass,
        array $items,
        ?object $_links = null,
        ?Response $response = null
    ): BaseCollection {
        return new $collectionClass($connector, $items, $_links, $response);
    }

    /**
     * @param  array|\ArrayObject  $data
     */
    private static function mapToResourceObjects(Connector $connector, $data, string $resourceClass, ?Response $response = null): array
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

    private static function determineCollectionClass(string $resourceClass, ?string $resourceCollectionClass): string
    {
        return $resourceCollectionClass ?: $resourceClass.'Collection';
    }
}
