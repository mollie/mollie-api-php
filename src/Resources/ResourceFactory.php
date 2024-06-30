<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\MollieApiClient;

#[\AllowDynamicProperties]
class ResourceFactory
{
    /**
     * Create resource object from Api result
     *
     * @param MollieApiClient $client
     * @param object $response
     * @param string $resourceClass
     * @return BaseResource
     */
    public static function createFromApiResult(MollieApiClient $client, object $response, string $resourceClass): BaseResource
    {
        /** @var BaseResource $resource */
        $resource = new $resourceClass($client);

        foreach ($response as $property => $value) {
            $resource->{$property} = self::holdsEmbeddedResources($resource, $property, $value)
                ? self::parseEmbeddedResources($client, $resource, $value)
                : $value;
        }

        return $resource;
    }

    /**
     * Check if the resource holds embedded resources
     *
     * @param object $resource
     * @param string $key
     * @param array|\ArrayAccess $value
     * @return bool
     */
    private static function holdsEmbeddedResources(object $resource, string $key, $value): bool
    {
        return $key === '_embedded'
            && ! is_null($value)
            && $resource instanceof EmbeddedResourcesContract;
    }

    /**
     * Parses embedded resources into their respective resource objects or collections.
     *
     * @param MollieApiClient $client
     * @param object $resource
     * @param object $embedded
     * @return object
     */
    private static function parseEmbeddedResources(MollieApiClient $client, object $resource, object $embedded): object
    {
        $result = new \stdClass();

        foreach ($embedded as $resourceKey => $resourceData) {
            $collectionOrResourceClass = $resource->getEmbeddedResourcesMap()[$resourceKey] ?? null;

            if (is_null($collectionOrResourceClass)) {
                throw new EmbeddedResourcesNotParseableException(
                    "Resource " . get_class($resource) . " does not have a mapping for embedded resource {$resourceKey}"
                );
            }

            $result->{$resourceKey} = is_subclass_of($collectionOrResourceClass, BaseResource::class)
                ? self::createFromApiResult(
                    $client,
                    $resourceData,
                    $collectionOrResourceClass
                )
                : self::createEmbeddedResourceCollection(
                    $client,
                    $collectionOrResourceClass,
                    $resourceData
                );
        }

        return $result;
    }

    /**
     * @param MollieApiClient $client
     * @param string $collectionClass
     * @param array|\ArrayObject $data
     * @return BaseCollection
     */
    private static function createEmbeddedResourceCollection(
        MollieApiClient $client,
        string $collectionClass,
        $data
    ): BaseCollection {
        return self::instantiateBaseCollection(
            $client,
            $collectionClass,
            self::mapToResourceObjects(
                $client,
                $data,
                $collectionClass::getResourceClass()
            ),
            null
        );
    }

    /**
     * @param MollieApiClient $client
     * @param string $resourceClass
     * @param null|array|\ArrayObject $data
     * @param object|null $_links
     * @param string|null $resourceCollectionClass
     * @return BaseCollection
     */
    public static function createBaseResourceCollection(
        MollieApiClient $client,
        string $resourceClass,
        $data = null,
        ?object $_links = null,
        ?string $resourceCollectionClass = null
    ): BaseCollection {
        return self::instantiateBaseCollection(
            $client,
            self::determineCollectionClass($resourceClass, $resourceCollectionClass),
            self::mapToResourceObjects($client, $data ?? [], $resourceClass),
            $_links
        );
    }

    /**
     * @param MollieApiClient $client
     * @param array|\ArrayObject $data
     * @param string $resourceClass
     * @param null $_links
     * @param null $resourceCollectionClass
     * @return CursorCollection
     */
    public static function createCursorResourceCollection(
        MollieApiClient $client,
        $data,
        string $resourceClass,
        ?object $_links = null,
        ?string $resourceCollectionClass = null
    ): CursorCollection {
        /** @var CursorCollection */
        return self::createBaseResourceCollection(
            $client,
            $resourceClass,
            $data,
            $_links,
            $resourceCollectionClass
        );
    }

    /**
     * @param MollieApiClient $client
     * @param string $collectionClass
     * @param array $items
     * @param object|null $_links
     * @return BaseCollection
     */
    private static function instantiateBaseCollection(MollieApiClient $client, string $collectionClass, array $items, ?object $_links): BaseCollection
    {
        return new $collectionClass($client, $items, $_links);
    }

    /**
     * @param MollieApiClient $client
     * @param array|\ArrayObject $data
     * @param string $resourceClass
     * @return array
     */
    private static function mapToResourceObjects(MollieApiClient $client, $data, string $resourceClass): array
    {
        return array_map(
            fn ($item) => static::createFromApiResult(
                $client,
                $item,
                $resourceClass
            ),
            (array) $data
        );
    }

    private static function determineCollectionClass(string $resourceClass, ?string $resourceCollectionClass): string
    {
        return $resourceCollectionClass ?: $resourceClass . 'Collection';
    }
}
