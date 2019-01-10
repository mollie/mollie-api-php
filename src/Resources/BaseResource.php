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
     * Create a resource collection from an array.
     *
     * @param array $input
     * @param string $resourceClass The full class namespace
     * @param null|object[] $_links
     * @param null $resourceCollectionClass If empty, appends 'Collection' to the `$resourceClass` to resolve the Collection class.
     * @return mixed
     */
    protected function createResourceCollection($input, $resourceClass, $_links = null, $resourceCollectionClass = null)
    {
        if (null === $resourceCollectionClass) {
            $resourceCollectionClass = $resourceClass.'Collection';
        }

        $data = new $resourceCollectionClass(count($input), $_links);
        foreach ($input as $item) {
            $data[] = ResourceFactory::createFromApiResult($item, new $resourceClass($this->client));
        }

        return $data;
    }
}