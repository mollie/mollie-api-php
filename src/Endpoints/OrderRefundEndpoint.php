<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Order;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

class OrderRefundEndpoint extends EndpointAbstract
{
    protected $resourcePath = "orders_refunds";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Refund
     */
    protected function getResourceObject()
    {
        return new Refund($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return RefundCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new RefundCollection($this->client, $count, $_links);
    }

    /**
     * Refund some order lines. You can provide an empty array for the
     * "lines" data to refund all eligable lines for this order.
     *
     * @param Order $order
     * @param array $data
     * @param array $filters
     *
     * @return Refund
     */
    public function createFor(Order $order, array $data, array $filters = [])
    {
        $this->parentId = $order->id;
        return parent::rest_create($data, $filters);
    }
}
