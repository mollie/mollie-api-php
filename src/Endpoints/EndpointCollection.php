<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\ResourceFactory;
use RuntimeException;

abstract class EndpointCollection extends RestEndpoint
{
    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = '';

    /**
     * Get a collection of objects from the REST API.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $filters
     *
     * @return BaseCollection
     * @throws ApiException
     */
    protected function fetchCollection(?string $from = null, ?int $limit = null, array $filters = []): BaseCollection
    {
        $apiPath = $this->getResourcePath() . $this->buildQueryString(
            $this->getMergedFilters($filters, $from, $limit)
        );

        $result = $this->client->performHttpCall(
            self::REST_LIST,
            $apiPath
        );

        return $this->buildResultCollection($result->decode());
    }

    /**
     * Create a generator for iterating over a resource's collection using REST API calls.
     *
     * This function fetches paginated data from a RESTful resource endpoint and returns a generator
     * that allows you to iterate through the items in the collection one by one. It supports forward
     * and backward iteration, pagination, and filtering.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $filters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     * @return LazyCollection
     */
    protected function createIterator(?string $from = null, ?int $limit = null, array $filters = [], bool $iterateBackwards = false): LazyCollection
    {
        /** @var CursorCollection $page */
        $page = $this->fetchCollection($from, $limit, $filters);

        return $page->getAutoIterator($iterateBackwards);
    }

    protected function getMergedFilters(array $filters = [], ?string $from = null, ?int $limit = null): array
    {
        return array_merge(["from" => $from, "limit" => $limit], $filters);
    }

    protected function buildResultCollection(object $result): BaseCollection
    {
        $collectionClass = $this->getResourceCollectionClass();

        return ResourceFactory::createBaseResourceCollection(
            $this->client,
            static::getResourceClass(),
            $result->_embedded->{$collectionClass::getCollectionResourceName()},
            $result->_links,
            $collectionClass
        );
    }

    /**
     * Get the collection class that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @return string
     */
    private function getResourceCollectionClass(): string
    {
        if (!isset(static::$resourceCollection)) {
            throw new RuntimeException("The resource collection class is not set on the endpoint.");
        }

        return static::$resourceCollection;
    }
}
