<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;

class OrderEndpoint extends EndpointAbstract
{
    protected $resourcePath = "orders";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Order
     */
    protected function getResourceObject()
    {
        return new Order($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return OrderCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new OrderCollection($this->api, $count, $_links);
    }

    /**
     * Creates a order in Mollie.
     *
     * @param array $data An array containing details on the order.
     * @param array $filters
     *
     * @return Order
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = [])
    {
        return $this->rest_create($data, $filters);
    }
}
