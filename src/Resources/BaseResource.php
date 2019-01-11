<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;

abstract class BaseResource
{
    /**
     * @var MollieApiClient
     */
    protected $client;

    /**
     * @param $client
     */
    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a base resource collection from an array.
     *
     * @param array $input
     * @param string $resourceClass The full class namespace
     * @param null|object[] $_links
     * @param null $resourceCollectionClass If empty, appends 'Collection' to the `$resourceClass` to resolve the Collection class.
     * @return mixed
     */
    protected function createBaseResourceCollection($input, $resourceClass, $_links = null, $resourceCollectionClass = null)
    {
        return ResourceFactory::createBaseResourceCollection($this->client, $input, $resourceClass, $_links, $resourceCollectionClass);
    }

    /**
     * Create a base resource collection from an array.
     *
     * @param array $input
     * @param string $resourceClass The full class namespace
     * @param null|object[] $_links
     * @param null $resourceCollectionClass If empty, appends 'Collection' to the `$resourceClass` to resolve the Collection class.
     * @return mixed
     */
    protected function createCursorResourceCollection($input, $resourceClass, $_links, $resourceCollectionClass = null)
    {
        return ResourceFactory::createCursorResourceCollection(
            $this->client,
            $input,
            $resourceClass,
            $_links,
            $resourceCollectionClass
        );
    }
}