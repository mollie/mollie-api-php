<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Route;
use Mollie\Api\Resources\RouteCollection;

class RouteEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = "route";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return \Mollie\Api\Resources\Route
     */
    protected function getResourceObject()
    {
        return new Route($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param \stdClass $_links
     *
     * @return \Mollie\Api\Resources\RouteCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new RouteCollection($this->client, $count, $_links);
    }
}
