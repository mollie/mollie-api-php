<?php

namespace Mollie\Api\Endpoints;

use Generator;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\ResourceFactory;

abstract class CollectionEndpointAbstract extends EndpointAbstract
{
    /**
     * Get a collection of objects from the REST API.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $filters
     *
     * @return mixed
     * @throws ApiException
     */
    protected function rest_list($from = null, $limit = null, array $filters = [])
    {
        $filters = array_merge(["from" => $from, "limit" => $limit], $filters);

        $apiPath = $this->getResourcePath() . $this->buildQueryString($filters);

        $result = $this->client->performHttpCall(self::REST_LIST, $apiPath);

        /** @var BaseCollection $collection */
        $collection = $this->getResourceCollectionObject($result->count, $result->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $dataResult) {
            $collection[] = ResourceFactory::createFromApiResult($dataResult, $this->getResourceObject());
        }

        return $collection;
    }

    /**
     * Iterate over all objects from the REST API.
     *
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $filters
     * @param boolean $iterateBackwards Whether to iterate backwards or forward.
     * @return Generator
     */
    protected function rest_iterator($from = null, $limit = null, array $filters = [], bool $iterateBackwards = false): Generator
    {
        /** @var CursorCollection $page */
        $page = $this->rest_list($from, $limit, $filters);

        while (true) {
            foreach ($page as $item) {
                yield $item;
            }

            if (($iterateBackwards && !$page->hasPrevious()) || !$page->hasNext()) {
                break;
            }

            $page = $iterateBackwards
                ? $page->previous()
                : $page->next();
        }
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return BaseCollection
     */
    abstract protected function getResourceCollectionObject($count, $_links);
}
