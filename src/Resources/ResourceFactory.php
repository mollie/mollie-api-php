<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;
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
            if ($property === '_embedded' && $resource instanceof EmbeddedResourcesContract) {
                $resource->_embedded = new \stdClass();

                foreach ($value as $embeddedResourceName => $embeddedResourceData) {
                    $resource->_embedded->{$embeddedResourceName} = self::createEmbeddedCollection(
                        $client,
                        $resource,
                        $embeddedResourceName,
                        $embeddedResourceData
                    );
                }
            } else {
                $resource->{$property} = $value;
            }
        }

        return $resource;
    }

    /**
     * @param MollieApiClient $client
     * @param $data
     * @return BaseCollection
     */
    private static function createEmbeddedCollection(
        MollieApiClient $client,
        EmbeddedResourcesContract $resource,
        string $collectionKey,
        $data
    ): BaseCollection {
        $collectionClass = $resource->getEmbeddedResourcesMap()[$collectionKey] ?? BaseCollection::class;

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
            fn (\stdClass $item) => static::createFromApiResult(
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
